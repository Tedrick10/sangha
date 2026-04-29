<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Monastery;
use App\Models\Sangha;
use App\Notifications\Monastery\SanghaApplicationDecidedNotification;
use App\Support\ExamEligibleSnapshot;
use App\Support\EligibleRollNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SanghaController extends Controller
{
    /** @var array<string, string> */
    private const PORTAL_PROGRAMME_ENTITY_TYPES = [
        'primary' => 'programme_primary',
        'intermediate' => 'programme_intermediate',
        'level-1' => 'programme_level_1',
        'level-2' => 'programme_level_2',
        'level-3' => 'programme_level_3',
    ];

    public function index(Request $request): View
    {
        $query = Sangha::with(['monastery', 'exam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('eligible_roll_number', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('nrc_number', 'like', "%{$search}%")
                    ->orWhereHas('monastery', fn ($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('monastery_id')) {
            $query->where('monastery_id', $request->monastery_id);
        }
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('exam_type_id')) {
            $typeId = (int) $request->exam_type_id;
            if ($typeId > 0) {
                $query->whereHas('exam', fn ($q) => $q->where('exam_type_id', $typeId));
            }
        }
        if ($request->filled('moderation_status')) {
            if ($request->moderation_status === 'approved') {
                $query->where('sanghas.workflow_status', Sangha::STATUS_APPROVED);
            } elseif ($request->moderation_status === 'eligible') {
                $query->where('sanghas.workflow_status', Sangha::STATUS_ELIGIBLE);
            } elseif ($request->moderation_status === 'pending') {
                $query->where('sanghas.workflow_status', Sangha::STATUS_PENDING);
            } elseif ($request->moderation_status === 'needed_update') {
                $query->where('sanghas.workflow_status', Sangha::STATUS_NEEDED_UPDATE);
            } elseif ($request->moderation_status === 'rejected') {
                $query->where('sanghas.workflow_status', Sangha::STATUS_REJECTED);
            }
        }

        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortCols = ['name', 'username', 'eligible_roll_number', 'created_at'];
        if ($sort === 'monastery') {
            $query->join('monasteries', 'sanghas.monastery_id', '=', 'monasteries.id')
                ->orderBy('monasteries.name', $order)
                ->select('sanghas.*');
        } elseif ($sort === 'exam') {
            $query->leftJoin('exams', 'sanghas.exam_id', '=', 'exams.id')
                ->orderBy('exams.name', $order)
                ->select('sanghas.*');
        } elseif (in_array($sort, $sortCols)) {
            $query->orderBy('sanghas.'.$sort, $order);
        } else {
            $query->latest();
        }
        $sanghas = $query->paginate(admin_per_page(10))->withQueryString();
        $monasteries = Monastery::orderBy('name')->get();
        $exams = Exam::orderBy('exam_date', 'desc')->orderBy('name')->get();
        $examTypes = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get();

        return view('admin.sanghas.index', compact('sanghas', 'monasteries', 'exams', 'examTypes'));
    }

    public function generateEligibleList(Request $request): RedirectResponse
    {
        $examQuery = Exam::query();

        if ($request->filled('exam_id')) {
            $examQuery->where('id', (int) $request->input('exam_id'));
        } elseif ($request->filled('exam_type_id')) {
            $examQuery->where('exam_type_id', (int) $request->input('exam_type_id'));
        }

        $exams = $examQuery->get();
        $generated = 0;
        foreach ($exams as $exam) {
            ExamEligibleSnapshot::upsertFromExam($exam);
            $generated++;
        }

        return redirect()
            ->route('admin.sanghas.index', $request->except('_token'))
            ->with('success', "Generated eligible list for {$generated} exam(s).");
    }

    public function show(Sangha $sangha): View
    {
        $sangha->load('monastery');
        $exams = Exam::whereHas('scores', fn ($q) => $q->where('sangha_id', $sangha->id))
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('admin.sanghas.show', compact('sangha', 'exams'));
    }

    public function customFieldFile(Sangha $sangha, CustomField $customField)
    {
        $value = CustomFieldValue::query()
            ->where('custom_field_id', $customField->id)
            ->where('entity_type', $customField->entity_type)
            ->where('entity_id', $sangha->id)
            ->value('value');

        abort_unless(is_string($value) && $value !== '', 404);
        abort_unless(\Storage::disk('public')->exists($value), 404);

        $mime = \Storage::disk('public')->mimeType($value) ?: 'application/octet-stream';
        $filename = basename($value);

        return \Storage::disk('public')->response($value, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function examScores(Sangha $sangha, Exam $exam): View
    {
        $scores = $sangha->scores()
            ->with('subject')
            ->where('exam_id', $exam->id)
            ->orderBy('subject_id')
            ->get();

        return view('admin.sanghas.exam-scores', compact('sangha', 'exam', 'scores'));
    }

    public function create(): View
    {
        $monasteries = Monastery::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();
        $customFields = CustomField::forEntity('sangha')->where('is_built_in', false)->get();
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();

        return view('admin.sanghas.create', compact('monasteries', 'exams', 'customFields', 'sanghaFieldMeta'));
    }

    public function store(Request $request): RedirectResponse
    {
        $bySlug = CustomField::sanghaDefinitionsBySlug();
        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, [
                'monastery_id',
                'exam_id',
                'name',
                'father_name',
                'nrc_number',
                'username',
                'description',
            ], null, 'any'),
            [
                'monastery_id' => ['required', Rule::exists('monasteries', 'id')],
            ]
        ));
        $this->finalizeSanghaAdminCoreFields($validated);
        $validated['is_active'] = true;
        $validated['approved'] = false;
        $validated['workflow_status'] = Sangha::STATUS_PENDING;
        $validated['rejection_reason'] = null;

        $sangha = Sangha::create($validated);
        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha created successfully.');
    }

    public function edit(Sangha $sangha): View
    {
        $monasteries = Monastery::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();
        $customFields = CustomField::forEntity('sangha')->where('is_built_in', false)->get();
        $customFieldValues = $sangha->getCustomFieldValuesArray();
        $programmeEntityType = $this->programmeEntityTypeForSangha($sangha);
        $programmeCustomFields = $programmeEntityType ? CustomField::forEntity($programmeEntityType)->get() : collect();
        $programmeCustomFieldValues = $programmeEntityType
            ? $this->existingCustomFieldValuesBySlug($sangha, $programmeCustomFields, $programmeEntityType)
            : [];
        $showSanghaExtraFields = $programmeEntityType === null;
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();

        return view('admin.sanghas.edit', compact(
            'sangha',
            'monasteries',
            'exams',
            'customFields',
            'customFieldValues',
            'programmeCustomFields',
            'programmeCustomFieldValues',
            'programmeEntityType',
            'showSanghaExtraFields',
            'sanghaFieldMeta'
        ));
    }

    public function update(Request $request, Sangha $sangha): RedirectResponse
    {
        $bySlug = CustomField::sanghaDefinitionsBySlug();
        $programmeEntityType = $this->programmeEntityTypeForSangha($sangha);
        $programmeCustomFields = $programmeEntityType ? CustomField::forEntity($programmeEntityType)->get() : collect();
        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, [
                'monastery_id',
                'exam_id',
                'name',
                'father_name',
                'nrc_number',
                'username',
                'description',
            ], $sangha->id, 'any'),
            [
                'monastery_id' => ['required', Rule::exists('monasteries', 'id')],
                'moderation_status' => 'nullable|in:eligible,pending,approved,needed_update,rejected',
                'rejection_reason' => 'nullable|string|required_if:moderation_status,rejected,needed_update|max:2000',
            ]
        ));
        $this->finalizeSanghaAdminCoreFields($validated, $sangha);
        $this->applyModerationState($validated, $request, $sangha);

        $beforeStatus = $sangha->moderationStatus();
        $sangha->update($validated);
        $sangha->refresh();
        $afterStatus = $sangha->moderationStatus();
        if ($beforeStatus !== $afterStatus && in_array($afterStatus, ['approved', 'needed_update', 'rejected'], true)) {
            $sangha->load('monastery');
            if ($sangha->monastery) {
                $screen = $afterStatus === 'approved'
                    ? 'approved'
                    : ($afterStatus === 'needed_update' ? 'needed-update' : 'rejected');
                $actionUrl = route('monastery.dashboard', ['tab' => 'main', 'screen' => $screen]);
                $preview = in_array($afterStatus, ['rejected', 'needed_update'], true) && filled($sangha->rejection_reason)
                    ? Str::limit($sangha->rejection_reason, 160)
                    : null;
                $sangha->monastery->notify(new SanghaApplicationDecidedNotification(
                    $sangha->name,
                    $afterStatus,
                    $preview,
                    $actionUrl,
                ));
            }
        }

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);
        if (isset($programmeEntityType) && $programmeEntityType && $programmeCustomFields->isNotEmpty()) {
            $this->persistCustomFieldValuesForEntity($sangha, $programmeCustomFields, $programmeEntityType, $request);
        }

        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha updated successfully.');
    }

    public function destroy(Sangha $sangha): RedirectResponse
    {
        $sangha->delete();

        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha deleted successfully.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function finalizeSanghaAdminCoreFields(array &$validated, ?Sangha $existing = null): void
    {
        $name = trim((string) ($validated['name'] ?? ''));
        if ($name === '') {
            $validated['name'] = $existing !== null
                ? $existing->name
                : ('Candidate '.Str::upper(Str::random(8)));
        }
        if (($validated['username'] ?? null) === '') {
            $validated['username'] = null;
        }
    }

    private function applyModerationState(array &$validated, Request $request, ?Sangha $existing = null): void
    {
        $status = $request->input('moderation_status');
        if (! in_array($status, ['eligible', 'pending', 'approved', 'needed_update', 'rejected'], true)) {
            $status = $existing ? $existing->moderationStatus() : 'pending';
        }

        $validated['approved'] = $status === Sangha::STATUS_APPROVED;
        $validated['workflow_status'] = $status;
        $validated['rejection_reason'] = in_array($status, [Sangha::STATUS_REJECTED, Sangha::STATUS_NEEDED_UPDATE], true)
            ? trim((string) $request->input('rejection_reason'))
            : null;

        $incomingExamId = array_key_exists('exam_id', $validated)
            ? ($validated['exam_id'] !== null ? (int) $validated['exam_id'] : null)
            : ($existing?->exam_id !== null ? (int) $existing->exam_id : null);
        $examChanged = $existing !== null && array_key_exists('exam_id', $validated)
            && (int) $existing->exam_id !== (int) ($validated['exam_id'] ?? 0);

        if ($status === Sangha::STATUS_ELIGIBLE && $existing !== null) {
            $currentRoll = $existing->eligible_roll_number;
            if ($currentRoll === null || $currentRoll === '') {
                $monasteryId = (int) ($validated['monastery_id'] ?? $existing->monastery_id);
                if ($monasteryId > 0) {
                    $validated['eligible_roll_number'] = EligibleRollNumberGenerator::next(
                        $monasteryId,
                        $this->programmeEntityTypeForSangha($existing)
                    );
                }
            }
        }

        if ($status === Sangha::STATUS_APPROVED) {
            $needsDeskNumber = $existing === null
                || ! filled($existing->desk_number)
                || $examChanged;

            if ($needsDeskNumber && $existing !== null) {
                $mid = (int) ($validated['monastery_id'] ?? $existing->monastery_id);
                if ($mid > 0) {
                    $validated['desk_number'] = $this->nextDeskNumberForMonasteryProgramme(
                        $mid,
                        $this->programmeEntityTypeForSangha($existing)
                    );
                }
            }
        }

        unset($validated['moderation_status']);
    }

    /**
     * Next hall desk integer per monastery + programme hub (not per exam_id).
     */
    private function nextDeskNumberForMonasteryProgramme(int $monasteryId, ?string $programmeEntityType): int
    {
        $query = Sangha::query()
            ->where('monastery_id', $monasteryId)
            ->where('workflow_status', Sangha::STATUS_APPROVED)
            ->whereNotNull('desk_number');

        if (EligibleRollNumberGenerator::isProgrammeEntityType($programmeEntityType)) {
            $query->whereExists(function ($sub) use ($programmeEntityType) {
                $sub->select(DB::raw(1))
                    ->from('custom_field_values')
                    ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                    ->where('custom_field_values.entity_type', $programmeEntityType);
            });
        } else {
            $query->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('custom_field_values')
                    ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                    ->whereIn('custom_field_values.entity_type', EligibleRollNumberGenerator::PROGRAMME_ENTITY_TYPES);
            });
        }

        $max = $query->max('desk_number');

        return ((int) $max) + 1;
    }

    private function programmeEntityTypeForSangha(Sangha $sangha): ?string
    {
        $known = array_values(self::PORTAL_PROGRAMME_ENTITY_TYPES);

        return CustomFieldValue::query()
            ->where('entity_id', $sangha->id)
            ->whereIn('entity_type', $known)
            ->value('entity_type');
    }

    /**
     * @return array<string, string>
     */
    private function existingCustomFieldValuesBySlug(Sangha $sangha, Collection $fields, string $entityType): array
    {
        $byFieldId = CustomFieldValue::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $sangha->id)
            ->pluck('value', 'custom_field_id');

        $out = [];
        foreach ($fields as $field) {
            $value = $byFieldId->get($field->id);
            if ($value !== null) {
                $out[$field->slug] = (string) $value;
            }
        }

        return $out;
    }

    private function persistCustomFieldValuesForEntity(Sangha $sangha, Collection $fields, string $entityType, Request $request): void
    {
        $values = $request->input('custom_fields', []);

        foreach ($fields as $field) {
            $value = $values[$field->slug] ?? null;
            if (in_array($field->type, ['media', 'document', 'video'], true)) {
                $file = $request->file('custom_fields.'.$field->slug);
                if ($file?->isValid()) {
                    $value = $file->store('custom-fields/'.$entityType.'/'.$sangha->id, 'public');
                }
            }

            if ($value === null && ! array_key_exists($field->slug, $values)) {
                continue;
            }

            $stored = $value;
            if (is_array($stored) || is_bool($stored)) {
                $stored = json_encode($stored);
            }

            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $field->id,
                    'entity_type' => $entityType,
                    'entity_id' => $sangha->id,
                ],
                ['value' => (string) $stored]
            );
        }
    }
}
