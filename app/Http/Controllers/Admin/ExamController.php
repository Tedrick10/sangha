<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Sangha;
use App\Models\Subject;
use App\Support\ExamEligibleSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $query = Exam::with('examType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('exam_type_id')) {
            $query->where('exam_type_id', $request->exam_type_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $sortCols = ['name', 'exam_date', 'is_active'];
        $sort = $request->get('sort', 'exam_date');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';
        if ($sort === 'exam_type_location') {
            $query->leftJoin('exam_types', 'exams.exam_type_id', '=', 'exam_types.id')
                ->orderByRaw('COALESCE(exam_types.name, exams.location) '.($order === 'asc' ? 'ASC' : 'DESC'))
                ->select('exams.*');
        } elseif (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest('exam_date');
        }
        $exams = $query->paginate(admin_per_page(10))->withQueryString();
        $examTypes = ExamType::query()->orderByCanonical()->get();

        return view('admin.exams.index', compact('exams', 'examTypes'));
    }

    public function create(): View
    {
        $examTypes = ExamType::where('is_active', true)->orderByCanonical()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $customFields = CustomField::forEntity('exam')->where('is_built_in', false)->get();

        return view('admin.exams.create', compact('examTypes', 'subjects', 'customFields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->examValidationRules($request));
        $this->applyExamBuiltInDefaults($validated, $request, null);

        $exam = Exam::create($validated);
        $exam->subjects()->sync($request->input('subjects', []));
        $exam->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()
            ->route('admin.exams.entrances', $exam)
            ->with('success', t('exam_created_manage_entrance', 'Exam created. Assign entrance and desk numbers below.'));
    }

    public function edit(Exam $exam): View
    {
        $exam->load('subjects');
        $examTypes = ExamType::where('is_active', true)->orderByCanonical()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $customFields = CustomField::forEntity('exam')->where('is_built_in', false)->get();
        $customFieldValues = $exam->getCustomFieldValuesArray();

        return view('admin.exams.edit', compact('exam', 'examTypes', 'subjects', 'customFields', 'customFieldValues'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate($this->examValidationRules($request, $exam));
        $this->applyExamBuiltInDefaults($validated, $request, $exam);

        $exam->update($validated);
        $exam->subjects()->sync($request->input('subjects', []));
        $exam->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    /**
     * Publish seated candidates (approved + desk assigned) for this exam to the public website snapshot.
     */
    public function generateEligibleList(Request $request, Exam $exam): JsonResponse|RedirectResponse
    {
        ExamEligibleSnapshot::upsertFromExam($exam);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => t('admin_exam_generate_eligible_toast', 'Generated successfully.'),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Optional text shown before desk numbers (admin preview + public eligible list).
     */
    public function updateDeskNumberPrefix(Request $request, Exam $exam): JsonResponse
    {
        $validated = $request->validate([
            'desk_number_prefix' => ['nullable', 'string', 'max:80'],
        ]);
        $prefix = $validated['desk_number_prefix'] ?? null;
        $exam->update([
            'desk_number_prefix' => $prefix !== null && $prefix !== '' ? $prefix : null,
        ]);

        if (ExamEligibleSnapshot::forExamId($exam->id)) {
            ExamEligibleSnapshot::upsertFromExam($exam->fresh()->loadMissing('examType'));
        }

        return response()->json(['ok' => true]);
    }

    public function entrances(Request $request, Exam $exam): View
    {
        $exam->load('examType');

        $tab = $request->query('tab', 'entrance');
        if (! in_array($tab, ['entrance', 'approved'], true)) {
            $tab = 'entrance';
        }
        // Self-heal any old desk-number gaps so UI stays contiguous 1..n.
        $this->renumberDesksForExam($exam);

        // Everyone except those already seated for this exam (exam_id + desk_number set).
        $pending = Sangha::query()
            ->with(['monastery', 'exam'])
            ->whereNot(function ($q) use ($exam): void {
                $q->where('exam_id', $exam->id)->whereNotNull('desk_number');
            })
            ->orderBy('name')
            ->paginate(admin_per_page(15))
            ->withQueryString();

        $seated = Sangha::query()
            ->where('exam_id', $exam->id)
            ->whereNotNull('desk_number')
            ->with('monastery')
            ->orderBy('desk_number')
            ->get();

        return view('admin.exams.entrances', compact('exam', 'tab', 'pending', 'seated'));
    }

    public function confirmEntrance(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'sangha_id' => ['required', 'exists:sanghas,id'],
        ]);

        $result = DB::transaction(function () use ($validated, $exam) {
            $sangha = Sangha::query()
                ->whereKey($validated['sangha_id'])
                ->lockForUpdate()
                ->first();

            if (! $sangha || ! $this->canConfirmEntranceForExam($sangha, $exam)) {
                return false;
            }

            $this->assignEntrance($sangha, $exam);

            return true;
        });

        if (! $result) {
            return redirect()
                ->route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'entrance'])
                ->with('error', t('exam_entrance_invalid_sangha', 'This candidate is not eligible for entrance confirmation.'));
        }

        return redirect()
            ->route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'approved'])
            ->with('success', t('exam_entrance_confirmed', 'Entrance confirmed and desk number assigned.'));
    }

    public function confirmEntranceBulk(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'sangha_ids' => ['required', 'array', 'min:1'],
            'sangha_ids.*' => ['integer', 'exists:sanghas,id'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['sangha_ids'])));

        $confirmed = 0;
        DB::transaction(function () use ($exam, $ids, &$confirmed): void {
            foreach ($ids as $id) {
                $sangha = Sangha::query()
                    ->whereKey($id)
                    ->lockForUpdate()
                    ->first();
                if (! $sangha || ! $this->canConfirmEntranceForExam($sangha, $exam)) {
                    continue;
                }
                $this->assignEntrance($sangha, $exam);
                $confirmed++;
            }
        });

        return redirect()
            ->route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'approved'])
            ->with('success', strtr(
                t('exam_entrance_bulk_confirmed', ':count candidate(s) confirmed.'),
                [':count' => (string) $confirmed]
            ));
    }

    /**
     * Clear desk assignment so the sangha returns to the Entrance queue for this exam.
     */
    public function unseatEntrance(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'sangha_id' => ['required', 'exists:sanghas,id'],
        ]);

        $result = DB::transaction(function () use ($validated, $exam) {
            $sangha = Sangha::query()
                ->whereKey($validated['sangha_id'])
                ->lockForUpdate()
                ->first();

            if (! $sangha || $sangha->exam_id !== $exam->id || $sangha->desk_number === null) {
                return false;
            }

            $sangha->update(['desk_number' => null]);
            $this->renumberDesksForExam($exam);

            return true;
        });

        if (! $result) {
            return redirect()
                ->route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'approved'])
                ->with('error', t('exam_entrance_remove_invalid', 'Could not remove this candidate.'));
        }

        return redirect()
            ->route('admin.exams.entrances', ['exam' => $exam, 'tab' => 'approved'])
            ->with('success', t('exam_entrance_removed', 'Candidate removed from approved list. They can be confirmed again from the Entrance tab.'));
    }

    /**
     * After removals, keep desk numbers contiguous 1…n in current sort order.
     */
    private function renumberDesksForExam(Exam $exam): void
    {
        DB::transaction(function () use ($exam): void {
            $seated = Sangha::query()
                ->where('exam_id', $exam->id)
                ->whereNotNull('desk_number')
                ->orderBy('desk_number')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($seated as $index => $sangha) {
                $newDesk = $index + 1;
                if ((int) $sangha->desk_number !== $newDesk) {
                    $sangha->update(['desk_number' => $newDesk]);
                }
            }
        });
    }

    private function nextDeskNumber(Exam $exam): int
    {
        $max = Sangha::query()->where('exam_id', $exam->id)->whereNotNull('desk_number')->max('desk_number');

        return (int) $max + 1;
    }

    private function canConfirmEntranceForExam(Sangha $sangha, Exam $exam): bool
    {
        return ! ($sangha->exam_id === $exam->id && $sangha->desk_number !== null);
    }

    private function assignEntrance(Sangha $sangha, Exam $exam): void
    {
        if ($sangha->exam_id !== $exam->id) {
            $sangha->update(['exam_id' => $exam->id]);
        }

        $sangha->refresh();
        $next = $this->nextDeskNumber($exam);
        $sangha->update(['desk_number' => $next]);
    }

    /**
     * Same name may exist for different dates or exam types; block exact duplicate triple.
     */
    /**
     * @return array<string, mixed>
     */
    private function examValidationRules(Request $request, ?Exam $exam = null): array
    {
        $ignoreId = $exam?->id;
        $nameBase = CustomField::isBuiltInSlugSuppressed('exam', 'name')
            ? ['nullable', 'string', 'max:255']
            : ['required', 'string', 'max:255'];

        $examTypeRule = CustomField::isBuiltInSlugSuppressed('exam', 'exam_type_id')
            ? 'nullable|exists:exam_types,id'
            : 'required|exists:exam_types,id';

        $rules = [
            'name' => array_merge($nameBase, [$this->uniqueExamNameRule($request, $ignoreId)]),
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'exam_type_id' => $examTypeRule,
            'location' => 'nullable|string|max:255',
        ];
        if (! CustomField::isBuiltInSlugSuppressed('exam', 'is_active')) {
            $rules['is_active'] = 'boolean';
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function applyExamBuiltInDefaults(array &$validated, Request $request, ?Exam $exam): void
    {
        if (CustomField::isBuiltInSlugSuppressed('exam', 'name')) {
            $n = trim((string) ($validated['name'] ?? ''));
            if ($n === '' && $exam !== null) {
                $validated['name'] = $exam->name;
            } elseif ($n === '') {
                do {
                    $candidate = substr('Exam '.Str::uuid()->toString(), 0, 255);
                } while (
                    Exam::query()
                        ->where('name', $candidate)
                        ->where(function ($query) use ($request): void {
                            if ($request->filled('exam_type_id')) {
                                $query->where('exam_type_id', (int) $request->input('exam_type_id'));
                            } else {
                                $query->whereNull('exam_type_id');
                            }
                            if ($request->filled('exam_date')) {
                                $query->whereDate('exam_date', $request->input('exam_date'));
                            } else {
                                $query->whereNull('exam_date');
                            }
                        })
                        ->exists()
                );
                $validated['name'] = $candidate;
            }
        }

        if (CustomField::isBuiltInSlugSuppressed('exam', 'is_active')) {
            $validated['is_active'] = $exam !== null ? $exam->is_active : true;
        } else {
            $validated['is_active'] = $request->boolean('is_active');
        }
    }

    private function uniqueExamNameRule(Request $request, ?int $ignoreExamId = null): Unique
    {
        $rule = Rule::unique('exams', 'name')->where(function ($query) use ($request) {
            if ($request->filled('exam_type_id')) {
                $query->where('exam_type_id', (int) $request->input('exam_type_id'));
            } else {
                $query->whereNull('exam_type_id');
            }
            if ($request->filled('exam_date')) {
                $query->whereDate('exam_date', $request->input('exam_date'));
            } else {
                $query->whereNull('exam_date');
            }
        });

        return $ignoreExamId !== null ? $rule->ignore($ignoreExamId) : $rule;
    }
}
