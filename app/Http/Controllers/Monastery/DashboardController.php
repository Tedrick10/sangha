<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Monastery;
use App\Models\MonasteryFormRequest;
use App\Models\MonasteryMessage;
use App\Models\Sangha;
use App\Models\Score;
use App\Models\SiteSetting;
use App\Notifications\Admin\NewMonasteryRequestNotification;
use App\Notifications\Admin\NewPendingSanghaNotification;
use App\Support\AdminNotifications;
use App\Support\EligibleRollNumberGenerator;
use App\Support\MonasteryPortalResultsSnapshot;
use App\Support\MonasteryResultsExplorer;
use App\Support\PassSanghaListDisplay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** @var list<string> */
    private const PORTAL_PROGRAMME_HUB_SCREENS = ['primary', 'intermediate', 'level-1', 'level-2', 'level-3'];

    /** @var array<string, string> */
    private const PORTAL_PROGRAMME_ENTITY_TYPES = [
        'primary' => 'programme_primary',
        'intermediate' => 'programme_intermediate',
        'level-1' => 'programme_level_1',
        'level-2' => 'programme_level_2',
        'level-3' => 'programme_level_3',
    ];

    public function __invoke(Request $request): View
    {
        $tab = in_array($request->get('tab'), ['main', 'results', 'exam', 'chat'], true)
            ? $request->get('tab')
            : 'main';

        $allowedScreens = ['main-home', 'results-home', 'results-year', 'results-level', 'results-exam', 'exam-home', 'total', 'primary', 'intermediate', 'level-1', 'level-2', 'level-3', 'eligible', 'needed-update', 'register', 'pending', 'approved', 'rejected', 'request', 'pass', 'fail', 'chat'];
        if ($tab === 'chat') {
            $screen = 'chat';
        } elseif ($tab === 'exam') {
            $screen = 'exam-home';
        } else {
            $screen = in_array($request->get('screen'), $allowedScreens, true)
                ? $request->get('screen')
                : ($tab === 'results' ? 'results-home' : 'main-home');
        }

        $monastery = Auth::guard('monastery')->user();
        $monastery->loadCount(['sanghas']);

        $portalProgrammeHubScreens = self::PORTAL_PROGRAMME_HUB_SCREENS;
        $programmeContext = in_array($request->query('programme'), $portalProgrammeHubScreens, true)
            ? $request->query('programme')
            : null;
        $portalFilters = $this->portalFiltersFromRequest($request);
        if ($screen !== 'total' && ($portalFilters['status'] ?? '') === 'pass_published') {
            $portalFilters['status'] = '';
        }

        $resultsRaw = SiteSetting::get(MonasteryPortalResultsSnapshot::key());
        $resultsDecoded = $resultsRaw ? json_decode($resultsRaw, true) : null;
        $resultsBlock = is_array($resultsDecoded)
            ? ($resultsDecoded['monasteries'][(string) $monastery->id] ?? null)
            : null;
        $resultsPublishedAt = is_array($resultsDecoded) ? ($resultsDecoded['generated_at'] ?? null) : null;

        $totalApplications = $monastery->sanghas_count;
        $eligibleCount = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->where('workflow_status', Sangha::STATUS_ELIGIBLE)
            ->count();
        $pendingCount = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->where('workflow_status', Sangha::STATUS_PENDING)
            ->count();
        $approvedCount = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->where('workflow_status', Sangha::STATUS_APPROVED)
            ->count();
        $neededUpdateCount = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->where('workflow_status', Sangha::STATUS_NEEDED_UPDATE)
            ->count();
        $rejectedCount = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->where('workflow_status', Sangha::STATUS_REJECTED)
            ->count();

        $eligibleSanghas = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->tap(fn ($q) => $this->applyPortalFilters($q, $portalFilters))
            ->with('exam')
            ->where('workflow_status', Sangha::STATUS_ELIGIBLE)
            ->orderByRaw('CASE WHEN eligible_roll_number IS NULL OR eligible_roll_number = "" THEN 1 ELSE 0 END')
            ->orderBy('eligible_roll_number')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $pendingSanghas = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->tap(fn ($q) => $this->applyPortalFilters($q, $portalFilters))
            ->with('exam')
            ->where('workflow_status', Sangha::STATUS_PENDING)
            ->orderByRaw('CASE WHEN eligible_roll_number IS NULL OR eligible_roll_number = "" THEN 1 ELSE 0 END')
            ->orderBy('eligible_roll_number')
            ->orderByDesc('id')
            ->limit(20)
            ->get();
        $approvedSanghas = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->tap(fn ($q) => $this->applyPortalFilters($q, $portalFilters))
            ->with('exam')
            ->where('workflow_status', Sangha::STATUS_APPROVED)
            ->orderByRaw('CASE WHEN eligible_roll_number IS NULL OR eligible_roll_number = "" THEN 1 ELSE 0 END')
            ->orderBy('eligible_roll_number')
            ->orderByDesc('id')
            ->limit(20)
            ->get();
        $neededUpdateSanghas = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->tap(fn ($q) => $this->applyPortalFilters($q, $portalFilters))
            ->with('exam')
            ->where('workflow_status', Sangha::STATUS_NEEDED_UPDATE)
            ->orderByRaw('CASE WHEN eligible_roll_number IS NULL OR eligible_roll_number = "" THEN 1 ELSE 0 END')
            ->orderBy('eligible_roll_number')
            ->orderByDesc('id')
            ->limit(20)
            ->get();
        $rejectedSanghas = $this->applyProgrammeScope($monastery->sanghas(), $programmeContext)
            ->tap(fn ($q) => $this->applyPortalFilters($q, $portalFilters))
            ->with('exam')
            ->where('workflow_status', Sangha::STATUS_REJECTED)
            ->orderByRaw('CASE WHEN eligible_roll_number IS NULL OR eligible_roll_number = "" THEN 1 ELSE 0 END')
            ->orderBy('eligible_roll_number')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $totalUnifiedRows = collect();
        if ($screen === 'total') {
            $totalUnifiedRows = $this->buildTotalUnifiedRows($monastery, $portalFilters, $resultsBlock);
        }

        $scoresCount = Score::whereHas('sangha', fn ($query) => $query->where('monastery_id', $monastery->id))->count();
        $recentScores = Score::whereHas('sangha', fn ($query) => $query->where('monastery_id', $monastery->id))
            ->with(['sangha:id,name', 'subject:id,name', 'exam:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();
        $sanghaCustomFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->where('slug', '!=', 'grade')
            ->get();
        if ($screen === 'request' || $tab === 'exam') {
            CustomField::syncBuiltInFieldDefinitions();
        }
        $requestCustomFields = CustomField::forEntity('request')->get();
        $monasteryExamCustomFields = CustomField::forEntity('monastery_exam')->get();

        $examTypesCanonical = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get();
        $canonicalExamTypeIds = $examTypesCanonical->pluck('id')->all();
        $examTypeId = (int) $request->get('exam_type_id', 0);
        if ($tab === 'exam') {
            if ($canonicalExamTypeIds === [] || ! in_array($examTypeId, $canonicalExamTypeIds, true)) {
                $examTypeId = (int) ($canonicalExamTypeIds[0] ?? 0);
            }
        } else {
            $examTypeId = 0;
        }

        $sanghaListProgrammeScreen = $programmeContext;
        if ($tab === 'exam' && $examTypeId > 0) {
            $mappedExamProgramme = $this->programmeScreenFromExamTypeId($examTypeId);
            if ($mappedExamProgramme !== null) {
                $sanghaListProgrammeScreen = $mappedExamProgramme;
            }
        }

        $monasteryExamApprovedSanghas = $this->applyProgrammeScope(
            $monastery->sanghas()->with('exam:id,name')->where('approved', true),
            $sanghaListProgrammeScreen
        )->orderBy('name')->get(['id', 'name', 'username', 'exam_id']);
        $monasteryApprovedSanghasForTransfer = $this->applyProgrammeScope(
            $monastery->sanghas()->with('exam:id,name')->where('approved', true),
            $programmeContext
        )->orderBy('name')->get(['id', 'name', 'username', 'exam_id']);

        $myFormRequests = $monastery->formRequests()->whereNull('exam_type_id')->latest()->limit(25)->get();
        $myExamFormRequests = $tab === 'exam' && $examTypeId > 0
            ? $monastery->formRequests()->where('exam_type_id', $examTypeId)->latest()->limit(25)->get()
            : collect();

        $monasteryExamCatalogYears = [];
        $monasteryExamCatalogByYear = [];
        if ($tab === 'exam') {
            $catalog = Exam::monasteryExamFormCatalog();
            $monasteryExamCatalogYears = $catalog['years'];
            $monasteryExamCatalogByYear = $catalog['byYear'];
        }

        $passSanghasAll = PassSanghaListDisplay::enrichSnapshotRows(
            collect($resultsBlock['pass'] ?? [])
        )->map(fn (array $row) => (object) $row);
        $failSanghasAll = PassSanghaListDisplay::enrichSnapshotRows(
            collect($resultsBlock['fail'] ?? [])
        )->map(fn (array $row) => (object) $row);

        $resultsExamId = max(0, (int) $request->query('results_exam_id', 0));
        $resultsYearKey = $request->query('results_year');
        $resultsYearKey = is_string($resultsYearKey) && $resultsYearKey !== '' ? $resultsYearKey : null;
        $resultsLevelKey = $request->query('results_level');
        $resultsLevelKey = is_string($resultsLevelKey) && $resultsLevelKey !== '' ? $resultsLevelKey : null;

        $passSanghas = $passSanghasAll;
        $failSanghas = $failSanghasAll;
        if ($resultsExamId > 0) {
            $passSanghas = MonasteryResultsExplorer::filterRows($passSanghasAll, null, null, $resultsExamId);
            $failSanghas = MonasteryResultsExplorer::filterRows($failSanghasAll, null, null, $resultsExamId);
        } elseif ($resultsYearKey !== null && $resultsLevelKey !== null) {
            $passSanghas = MonasteryResultsExplorer::filterRows($passSanghasAll, $resultsYearKey, $resultsLevelKey, null);
            $failSanghas = MonasteryResultsExplorer::filterRows($failSanghasAll, $resultsYearKey, $resultsLevelKey, null);
        } elseif ($resultsYearKey !== null) {
            $passSanghas = MonasteryResultsExplorer::filterRows($passSanghasAll, $resultsYearKey, null, null);
            $failSanghas = MonasteryResultsExplorer::filterRows($failSanghasAll, $resultsYearKey, null, null);
        }

        if ($tab === 'results') {
            if ($screen === 'results-year' && $resultsYearKey === null) {
                $screen = 'results-home';
            }
            if ($screen === 'results-level' && ($resultsYearKey === null || $resultsLevelKey === null)) {
                $screen = 'results-home';
            }
            if ($screen === 'results-exam' && $resultsExamId <= 0) {
                $screen = 'results-home';
            }
        }

        $resultsExplorerYears = MonasteryResultsExplorer::yearCards($passSanghasAll, $failSanghasAll);
        $resultsExplorerLevels = $screen === 'results-year' && $resultsYearKey !== null
            ? MonasteryResultsExplorer::levelCards($passSanghasAll, $failSanghasAll, $resultsYearKey)
            : collect();
        $resultsExplorerExams = $screen === 'results-level' && $resultsYearKey !== null && $resultsLevelKey !== null
            ? MonasteryResultsExplorer::examCards($passSanghasAll, $failSanghasAll, $resultsYearKey, $resultsLevelKey)
            : collect();

        $resultsExplorerExamSummary = null;
        if ($screen === 'results-exam' && $resultsExamId > 0) {
            $pExam = MonasteryResultsExplorer::filterRows($passSanghasAll, null, null, $resultsExamId);
            $fExam = MonasteryResultsExplorer::filterRows($failSanghasAll, null, null, $resultsExamId);
            if ($pExam->isEmpty() && $fExam->isEmpty()) {
                $screen = 'results-home';
            } else {
                $sample = $pExam->first() ?? $fExam->first();
                $resultsExplorerExamSummary = [
                    'exam_id' => $resultsExamId,
                    'exam_name' => $sample?->exam_name ?? ('#'.$resultsExamId),
                    'pass_count' => $pExam->count(),
                    'fail_count' => $fExam->count(),
                    'year_key' => $sample ? MonasteryResultsExplorer::yearKeyFromRow($sample) : MonasteryResultsExplorer::YEAR_UNKNOWN,
                    'level_key' => $sample ? MonasteryResultsExplorer::levelKeyFromRow($sample) : MonasteryResultsExplorer::LEVEL_NONE,
                ];
                if ($sample !== null) {
                    if ($resultsYearKey === null) {
                        $resultsYearKey = MonasteryResultsExplorer::yearKeyFromRow($sample);
                    }
                    if ($resultsLevelKey === null) {
                        $resultsLevelKey = MonasteryResultsExplorer::levelKeyFromRow($sample);
                    }
                }
            }
        }

        $recentChatMessages = collect();
        if ($tab === 'chat') {
            $recentChatMessages = MonasteryMessage::query()
                ->where('monastery_id', $monastery->id)
                ->with(['user:id,name', 'monastery:id,name'])
                ->orderByDesc('id')
                ->limit(100)
                ->get()
                ->reverse()
                ->values();
        }

        $activeProgrammeScreen = in_array($screen, $portalProgrammeHubScreens, true) ? $screen : $programmeContext;
        $programmeEntityType = $this->programmeEntityType($activeProgrammeScreen);
        $programmeCustomFields = $programmeEntityType
            ? CustomField::forEntity($programmeEntityType)->get()
            : collect();

        $editingFeedbackSangha = null;
        $sanghaEditCustomFieldDefaults = [];
        $programmeEditCustomFieldDefaults = [];
        if ($screen === 'register' && $request->filled('edit')) {
            $editId = (int) $request->query('edit');
            if ($editId > 0) {
                $candidate = $monastery->sanghas()->whereKey($editId)->first();
                if ($candidate
                    && ! $candidate->approved
                    && filled($candidate->rejection_reason)) {
                    $editingFeedbackSangha = $candidate->load('exam');
                    foreach ($sanghaCustomFields as $cf) {
                        $sanghaEditCustomFieldDefaults[$cf->slug] = $editingFeedbackSangha->getCustomFieldValue($cf->slug);
                    }
                    $programmeEditCustomFieldDefaults = $programmeEntityType
                        ? $this->existingCustomFieldValuesBySlug($editingFeedbackSangha, $programmeCustomFields, $programmeEntityType)
                        : [];
                }
            }
        }

        $portalSanghaDetailsById = [];
        $detailSanghas = match ($screen) {
            'eligible' => $eligibleSanghas,
            'pending' => $pendingSanghas,
            'approved' => $approvedSanghas,
            'needed-update' => $neededUpdateSanghas,
            'rejected' => $rejectedSanghas,
            'total' => $this->sanghasForPortalDetailsFromTotalRows($totalUnifiedRows),
            default => collect(),
        };
        if ($detailSanghas->isNotEmpty()) {
            foreach ($detailSanghas as $detailSangha) {
                $portalSanghaDetailsById[(string) $detailSangha->id] = $this->buildPortalEligibleSanghaViewDetails(
                    $detailSangha,
                    $sanghaFieldMeta,
                    $sanghaCustomFields,
                    $programmeContext
                );
            }
        }

        $monasterySelectMonasteries = collect();
        if ($screen === 'request') {
            $monasterySelectMonasteries = Monastery::query()
                ->where('is_active', true)
                ->where('approved', true)
                ->whereKeyNot($monastery->id)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('monastery.dashboard', compact(
            'monastery',
            'tab',
            'totalApplications',
            'eligibleCount',
            'pendingCount',
            'approvedCount',
            'neededUpdateCount',
            'rejectedCount',
            'eligibleSanghas',
            'pendingSanghas',
            'approvedSanghas',
            'neededUpdateSanghas',
            'rejectedSanghas',
            'scoresCount',
            'recentScores',
            'exams',
            'sanghaFieldMeta',
            'sanghaCustomFields',
            'requestCustomFields',
            'monasteryExamCustomFields',
            'monasteryExamApprovedSanghas',
            'monasteryApprovedSanghasForTransfer',
            'myFormRequests',
            'examTypesCanonical',
            'examTypeId',
            'myExamFormRequests',
            'screen',
            'passSanghas',
            'failSanghas',
            'passSanghasAll',
            'failSanghasAll',
            'resultsYearKey',
            'resultsLevelKey',
            'resultsExamId',
            'resultsExplorerYears',
            'resultsExplorerLevels',
            'resultsExplorerExams',
            'resultsExplorerExamSummary',
            'resultsPublishedAt',
            'recentChatMessages',
            'monasteryExamCatalogYears',
            'monasteryExamCatalogByYear',
            'editingFeedbackSangha',
            'sanghaEditCustomFieldDefaults',
            'programmeCustomFields',
            'programmeEditCustomFieldDefaults',
            'portalProgrammeHubScreens',
            'programmeContext',
            'portalSanghaDetailsById',
            'totalUnifiedRows',
            'portalFilters',
            'monasterySelectMonasteries',
        ));
    }

    public function storeSangha(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $programmeScreen = $this->programmeScreenFromRequest($request);
        $programmeEntityType = $this->programmeEntityType($programmeScreen);
        $customFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->where('slug', '!=', 'grade')
            ->get();
        $programmeFields = $programmeEntityType ? CustomField::forEntity($programmeEntityType)->get() : collect();
        $bySlug = CustomField::sanghaDefinitionsBySlug();

        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, ['exam_id', 'name', 'father_name', 'nrc_number', 'description']),
            $this->customFieldRules($customFields, $request),
            $this->customFieldRules($programmeFields, $request)
        ));

        $name = trim((string) ($validated['name'] ?? ''));
        if ($name === '') {
            $name = 'Candidate '.Str::upper(Str::random(8));
        }

        $sangha = Sangha::create([
            'monastery_id' => $monastery->id,
            'exam_id' => $validated['exam_id'] ?? null,
            'name' => $name,
            'father_name' => $validated['father_name'] ?? null,
            'nrc_number' => $validated['nrc_number'] ?? null,
            'username' => null,
            'password' => null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'approved' => false,
            'workflow_status' => Sangha::STATUS_ELIGIBLE,
            'eligible_roll_number' => null,
        ]);

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);
        if ($programmeEntityType) {
            $this->persistCustomFieldValuesForEntity($sangha, $programmeFields, $programmeEntityType, $request);
        }

        $sangha->eligible_roll_number = EligibleRollNumberGenerator::next((int) $monastery->id, $programmeEntityType);
        $sangha->save();

        return redirect()
            ->route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => 'eligible', 'programme' => $programmeScreen]))
            ->with('success', t('sangha_application_added_to_eligible', 'Sangha application added to Eligible. Submit it to admin when ready.'));
    }

    public function updateRejectedSangha(Request $request, Sangha $sangha): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        abort_unless((int) $sangha->monastery_id === (int) $monastery->id, 403);
        abort_unless(in_array($sangha->workflow_status, [Sangha::STATUS_REJECTED, Sangha::STATUS_NEEDED_UPDATE], true), 403);

        $programmeScreen = $this->programmeScreenFromRequest($request);
        $programmeEntityType = $this->programmeEntityType($programmeScreen);
        $customFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->where('slug', '!=', 'grade')
            ->get();
        $programmeFields = $programmeEntityType ? CustomField::forEntity($programmeEntityType)->get() : collect();
        $bySlug = CustomField::sanghaDefinitionsBySlug();

        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, ['exam_id', 'name', 'father_name', 'nrc_number', 'description']),
            $this->customFieldRules($customFields, $request, $sangha),
            $this->customFieldRules($programmeFields, $request)
        ));

        $name = trim((string) ($validated['name'] ?? ''));
        if ($name === '') {
            $name = 'Candidate '.Str::upper(Str::random(8));
        }

        $targetStatus = $sangha->workflow_status === Sangha::STATUS_NEEDED_UPDATE
            ? Sangha::STATUS_PENDING
            : Sangha::STATUS_ELIGIBLE;

        $needsNewRoll = ! filled($sangha->eligible_roll_number);

        $sangha->update([
            'exam_id' => $validated['exam_id'] ?? null,
            'name' => $name,
            'father_name' => $validated['father_name'] ?? null,
            'nrc_number' => $validated['nrc_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'rejection_reason' => null,
            'approved' => false,
            'workflow_status' => $targetStatus,
            'eligible_roll_number' => $needsNewRoll ? null : $sangha->eligible_roll_number,
        ]);

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);
        if ($programmeEntityType) {
            $this->persistCustomFieldValuesForEntity($sangha, $programmeFields, $programmeEntityType, $request);
        }

        if ($needsNewRoll) {
            $sangha->eligible_roll_number = EligibleRollNumberGenerator::next((int) $monastery->id, $programmeEntityType);
            $sangha->save();
        }

        $nextScreen = $targetStatus === Sangha::STATUS_PENDING ? 'pending' : 'eligible';
        $successKey = $targetStatus === Sangha::STATUS_PENDING
            ? 'sangha_application_resubmitted_to_pending'
            : 'sangha_application_back_to_eligible';
        $successDefault = $targetStatus === Sangha::STATUS_PENDING
            ? 'Application updated and resubmitted to Pending.'
            : 'Application updated and resubmitted to Eligible.';

        return redirect()
            ->route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => $nextScreen, 'programme' => $programmeScreen]))
            ->with('success', t($successKey, $successDefault));
    }

    public function submitEligibleSanghas(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $programmeScreen = $this->programmeScreenFromRequest($request);

        $ids = collect($request->input('sangha_ids', []))
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()
                ->route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => 'eligible', 'programme' => $programmeScreen]))
                ->with('error', t('eligible_submit_pick_one', 'Choose at least one Sangha from Eligible.'));
        }

        $eligibleSanghas = $this->applyProgrammeScope($monastery->sanghas(), $programmeScreen)
            ->whereIn('id', $ids->all())
            ->where('workflow_status', Sangha::STATUS_ELIGIBLE)
            ->get();

        if ($eligibleSanghas->isEmpty()) {
            return redirect()
                ->route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => 'eligible', 'programme' => $programmeScreen]))
                ->with('error', t('eligible_submit_not_found', 'Selected Sangha records are not in Eligible.'));
        }

        Sangha::query()
            ->whereIn('id', $eligibleSanghas->pluck('id')->all())
            ->update([
                'workflow_status' => Sangha::STATUS_PENDING,
                'approved' => false,
                'rejection_reason' => null,
            ]);

        foreach ($eligibleSanghas as $sangha) {
            AdminNotifications::notifyAll(new NewPendingSanghaNotification(
                $sangha->name,
                $monastery->name,
                t('notif_source_monastery_portal', 'Monastery portal'),
                route('admin.sanghas.edit', $sangha),
            ));
        }

        return redirect()
            ->route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => 'pending', 'programme' => $programmeScreen]))
            ->with('success', t('eligible_submit_success', ':count Sangha submitted to admin and moved to Pending.', ['count' => $eligibleSanghas->count()]));
    }

    public function customFieldFile(Sangha $sangha, CustomField $customField)
    {
        $monastery = Auth::guard('monastery')->user();
        abort_unless((int) $sangha->monastery_id === (int) $monastery->id, 403);
        abort_unless(in_array($customField->type, ['media', 'document', 'video'], true), 404);

        $value = CustomFieldValue::query()
            ->where('custom_field_id', $customField->id)
            ->where('entity_type', $customField->entity_type)
            ->where('entity_id', $sangha->id)
            ->value('value');

        abort_unless(is_string($value) && $value !== '', 404);
        abort_unless(Storage::disk('public')->exists($value), 404);

        $mime = Storage::disk('public')->mimeType($value) ?: 'application/octet-stream';
        $filename = basename($value);

        return Storage::disk('public')->response($value, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function storeFormRequest(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        CustomField::syncBuiltInFieldDefinitions();
        $requestCustomFields = CustomField::forEntity('request')->get();

        $transferReturnQuery = array_filter([
            'tab' => 'main',
            'screen' => 'request',
            'programme' => $this->programmeScreenFromRequest($request),
        ], fn ($v) => $v !== null && $v !== '');

        if ($requestCustomFields->isEmpty()) {
            return redirect()
                ->route('monastery.dashboard', $transferReturnQuery)
                ->with('error', t('no_request_fields_configured', 'No transfer form fields are configured yet. Please contact the administrator.'));
        }

        if ($requestCustomFields->firstWhere('slug', 'transfer_from')) {
            $cfIn = $request->input('custom_fields', []);
            if (! isset($cfIn['transfer_from']) || $cfIn['transfer_from'] === '' || $cfIn['transfer_from'] === null) {
                $cfIn['transfer_from'] = $monastery->name;
                $request->merge(['custom_fields' => $cfIn]);
            }
        }

        $request->validate($this->customFieldRules($requestCustomFields, $request));

        if (! $this->portalRequestHasAnyInput($request, $requestCustomFields)) {
            return redirect()
                ->route('monastery.dashboard', $transferReturnQuery)
                ->with('error', t('request_form_fill_one', 'Please complete the transfer form before submitting.'));
        }

        $submission = MonasteryFormRequest::create([
            'monastery_id' => $monastery->id,
            'status' => MonasteryFormRequest::STATUS_PENDING,
        ]);

        $submission->syncRequestFieldValues($request);

        $preview = $submission->fresh()->summaryPreview();
        if ($preview === '—') {
            $preview = t('notif_new_request_submitted', 'New transfer submitted');
        }

        AdminNotifications::notifyAll(new NewMonasteryRequestNotification(
            $monastery->name,
            Str::limit($preview, 140),
            route('admin.monastery-requests.show', $submission),
        ));

        return redirect()
            ->route('monastery.dashboard', $transferReturnQuery)
            ->with('success', t('request_submitted_success', 'Your transfer was submitted and is pending review.'));
    }

    public function storeExamFormSubmission(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $allowedExamTypeIds = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->pluck('id')
            ->all();

        CustomField::syncBuiltInFieldDefinitions();
        $monasteryExamCustomFields = CustomField::forEntity('monastery_exam')->get();

        if ($monasteryExamCustomFields->isEmpty()) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $request->input('exam_type_id')])
                ->with('error', t('no_monastery_exam_fields_configured', 'No monastery exam form fields are configured yet. Please contact the administrator.'));
        }

        $request->validate(array_merge(
            ['exam_type_id' => ['required', 'integer', Rule::in($allowedExamTypeIds)]],
            $this->customFieldRules($monasteryExamCustomFields, $request)
        ));

        if (! $this->portalRequestHasAnyInput($request, $monasteryExamCustomFields)) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $request->integer('exam_type_id')])
                ->with('error', t('exam_form_fill_one', 'Please complete the exam form before submitting.'));
        }

        $resolvedExamTypeId = (int) $request->input('exam_type_id');
        $sessionExamId = $request->input('custom_fields.exam_session');
        if (filled($sessionExamId) && ctype_digit((string) $sessionExamId)) {
            $picked = Exam::query()->find((int) $sessionExamId);
            if ($picked && $picked->exam_type_id !== null && in_array((int) $picked->exam_type_id, $allowedExamTypeIds, true)) {
                $resolvedExamTypeId = (int) $picked->exam_type_id;
            }
        }

        $submission = MonasteryFormRequest::create([
            'monastery_id' => $monastery->id,
            'exam_type_id' => $resolvedExamTypeId,
            'status' => MonasteryFormRequest::STATUS_PENDING,
        ]);

        $submission->syncRequestFieldValues($request);

        $submission = $submission->fresh(['examType']);
        $preview = $submission->summaryPreview();
        if ($preview === '—') {
            $preview = t('notif_new_request_submitted', 'New transfer submitted');
        }
        if ($submission->examType) {
            $preview = '['.$submission->examType->name.'] '.$preview;
        }

        AdminNotifications::notifyAll(new NewMonasteryRequestNotification(
            $monastery->name,
            Str::limit($preview, 140),
            route('admin.monastery-requests.show', $submission),
        ));

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $submission->exam_type_id])
            ->with('success', t('exam_form_submitted_success', 'Exam form submitted and is pending review.'));
    }

    private function portalRequestHasAnyInput(Request $request, Collection $fields): bool
    {
        $values = $request->input('custom_fields', []);

        foreach ($fields as $field) {
            if (in_array($field->type, ['media', 'document', 'video'], true)) {
                if ($request->hasFile('custom_fields.'.$field->slug)) {
                    return true;
                }
            } elseif ($field->type === 'checkbox') {
                if ($request->boolean('custom_fields.'.$field->slug)) {
                    return true;
                }
            } elseif (isset($values[$field->slug]) && filled($values[$field->slug])) {
                return true;
            }
        }

        return false;
    }

    private function programmeScreenFromRequest(Request $request): ?string
    {
        $programme = $request->input('programme');
        if (! is_string($programme) || $programme === '') {
            return null;
        }

        return in_array($programme, self::PORTAL_PROGRAMME_HUB_SCREENS, true) ? $programme : null;
    }

    private function programmeEntityType(?string $programmeScreen): ?string
    {
        if (! is_string($programmeScreen) || $programmeScreen === '') {
            return null;
        }

        return self::PORTAL_PROGRAMME_ENTITY_TYPES[$programmeScreen] ?? null;
    }

    /**
     * Map canonical exam type (Primary, Intermediate, …) to monastery portal programme screen slug.
     */
    private function programmeScreenFromExamTypeId(int $examTypeId): ?string
    {
        if ($examTypeId <= 0) {
            return null;
        }
        $name = ExamType::query()->whereKey($examTypeId)->value('name');
        if (! is_string($name) || $name === '') {
            return null;
        }

        return match ($name) {
            'Primary' => 'primary',
            'Intermediate' => 'intermediate',
            'Level 1' => 'level-1',
            'Level 2' => 'level-2',
            'Level 3' => 'level-3',
            default => null,
        };
    }

    private function applyProgrammeScope(HasMany|Builder $query, ?string $programmeScreen): HasMany|Builder
    {
        $entityType = $this->programmeEntityType($programmeScreen);
        if ($entityType === null) {
            return $query;
        }

        return $query->whereExists(function ($sub) use ($entityType) {
            $sub->selectRaw('1')
                ->from('custom_field_values')
                ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                ->where('custom_field_values.entity_type', $entityType);
        });
    }

    /**
     * @return array{
     *   roll_number: string,
     *   desk_number: string,
     *   sangha: string,
     *   exam_id: ?int,
     *   created_date: string,
     *   status: string
     * }
     */
    private function portalFiltersFromRequest(Request $request): array
    {
        $status = (string) $request->query('filter_status', '');
        $allowedStatus = [
            '',
            Sangha::STATUS_ELIGIBLE,
            Sangha::STATUS_PENDING,
            Sangha::STATUS_APPROVED,
            Sangha::STATUS_NEEDED_UPDATE,
            Sangha::STATUS_REJECTED,
            'pass_published',
        ];

        return [
            'roll_number' => trim((string) $request->query('filter_roll_number', '')),
            'desk_number' => trim((string) $request->query('filter_desk_number', '')),
            'sangha' => trim((string) $request->query('filter_sangha', '')),
            'exam_id' => $request->filled('filter_exam_id') ? (int) $request->query('filter_exam_id') : null,
            'created_date' => trim((string) $request->query('filter_created_date', '')),
            'status' => in_array($status, $allowedStatus, true) ? $status : '',
        ];
    }

    /**
     * @param  array{roll_number:string,desk_number:string,sangha:string,exam_id:?int,created_date:string,status:string}  $filters
     */
    private function applyPortalFilters(HasMany|Builder $query, array $filters): void
    {
        if ($filters['roll_number'] !== '') {
            $query->where('eligible_roll_number', 'like', '%'.$filters['roll_number'].'%');
        }

        if ($filters['desk_number'] !== '') {
            $desk = $filters['desk_number'];
            $query->where(function ($deskQuery) use ($desk) {
                $deskQuery->where('desk_number', 'like', '%'.$desk.'%')
                    ->orWhereHas('exam', fn ($examQ) => $examQ->where('desk_number_prefix', 'like', '%'.$desk.'%'));
            });
        }

        if ($filters['sangha'] !== '') {
            $query->where('name', 'like', '%'.$filters['sangha'].'%');
        }

        if ($filters['exam_id'] !== null && $filters['exam_id'] > 0) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if ($filters['created_date'] !== '') {
            $query->whereDate('created_at', $filters['created_date']);
        }

        if ($filters['status'] !== '' && $filters['status'] !== 'pass_published') {
            $query->where('workflow_status', $filters['status']);
        }
    }

    /**
     * @param  array|null  $resultsBlock  This monastery's pass/fail slice from {@see MonasteryPortalResultsSnapshot}
     * @return Collection<int, object{kind: string, sangha?: Sangha, pass?: object}>
     */
    private function buildTotalUnifiedRows($monastery, array $portalFilters, ?array $resultsBlock): Collection
    {
        $workflowStatuses = [
            Sangha::STATUS_ELIGIBLE,
            Sangha::STATUS_PENDING,
            Sangha::STATUS_APPROVED,
            Sangha::STATUS_NEEDED_UPDATE,
            Sangha::STATUS_REJECTED,
        ];

        $statusFilter = (string) ($portalFilters['status'] ?? '');
        $allowedWorkflow = $workflowStatuses;
        if ($statusFilter !== '' && $statusFilter !== 'pass_published' && in_array($statusFilter, $workflowStatuses, true)) {
            $allowedWorkflow = [$statusFilter];
        }

        $includeLive = $statusFilter !== 'pass_published';
        $includePass = $statusFilter === '' || $statusFilter === 'pass_published';

        $statusRank = [
            Sangha::STATUS_ELIGIBLE => 0,
            Sangha::STATUS_PENDING => 1,
            Sangha::STATUS_APPROVED => 2,
            Sangha::STATUS_NEEDED_UPDATE => 3,
            Sangha::STATUS_REJECTED => 4,
        ];

        $out = collect();

        if ($includeLive) {
            $liveFilters = $portalFilters;
            if (($liveFilters['status'] ?? '') === 'pass_published') {
                $liveFilters['status'] = '';
            }

            $live = $monastery->sanghas()
                ->tap(fn ($q) => $this->applyPortalFilters($q, $liveFilters))
                ->with('exam')
                ->whereIn('workflow_status', $allowedWorkflow)
                ->orderByRaw('CASE WHEN eligible_roll_number IS NULL OR eligible_roll_number = "" THEN 1 ELSE 0 END')
                ->orderBy('eligible_roll_number')
                ->orderByDesc('id')
                ->limit(800)
                ->get()
                ->sort(function (Sangha $a, Sangha $b) use ($statusRank): int {
                    $ra = $statusRank[$a->workflow_status] ?? 99;
                    $rb = $statusRank[$b->workflow_status] ?? 99;
                    if ($ra !== $rb) {
                        return $ra <=> $rb;
                    }
                    $rollA = (string) ($a->eligible_roll_number ?? '');
                    $rollB = (string) ($b->eligible_roll_number ?? '');
                    if ($rollA !== $rollB) {
                        return strcmp($rollA, $rollB);
                    }

                    return $b->id <=> $a->id;
                })
                ->values();

            foreach ($live as $sangha) {
                $out->push((object) ['kind' => 'sangha', 'sangha' => $sangha]);
            }
        }

        if ($includePass && is_array($resultsBlock)) {
            $passSorted = PassSanghaListDisplay::enrichSnapshotRows(collect($resultsBlock['pass'] ?? []))
                ->filter(function ($row) use ($portalFilters) {
                    $arr = is_array($row) ? $row : (array) $row;

                    return $this->portalPassSnapshotRowMatchesFilters($arr, $portalFilters);
                })
                ->values()
                ->sort(function ($a, $b): int {
                    $ae = is_array($a) ? $a : (array) $a;
                    $be = is_array($b) ? $b : (array) $b;
                    $y = strcmp((string) ($be['exam_year'] ?? ''), (string) ($ae['exam_year'] ?? ''));
                    if ($y !== 0) {
                        return $y;
                    }

                    return strcmp((string) ($ae['name'] ?? ''), (string) ($be['name'] ?? ''));
                })
                ->values();

            foreach ($passSorted as $row) {
                $arr = is_array($row) ? $row : (array) $row;
                $out->push((object) ['kind' => 'pass', 'pass' => (object) $arr]);
            }
        }

        return $out;
    }

    /**
     * @param  Collection<int, object{kind: string, sangha?: Sangha, pass?: object}>  $rows
     * @return Collection<int, Sangha>
     */
    private function sanghasForPortalDetailsFromTotalRows(Collection $rows): Collection
    {
        $ids = collect();
        foreach ($rows as $row) {
            if (($row->kind ?? '') === 'sangha' && isset($row->sangha) && $row->sangha instanceof Sangha) {
                $ids->push((int) $row->sangha->id);
            }
            if (($row->kind ?? '') === 'pass' && isset($row->pass->id)) {
                $ids->push((int) $row->pass->id);
            }
        }

        $unique = $ids->unique()->filter(fn (int $id): bool => $id > 0)->values()->all();
        if ($unique === []) {
            return collect();
        }

        return Sangha::query()->whereIn('id', $unique)->with('exam')->get();
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array{roll_number:string,desk_number:string,sangha:string,exam_id:?int,created_date:string,status:string}  $filters
     */
    private function portalPassSnapshotRowMatchesFilters(array $row, array $filters): bool
    {
        $st = (string) ($filters['status'] ?? '');
        if ($st !== '' && $st !== 'pass_published') {
            return false;
        }

        if (($filters['created_date'] ?? '') !== '') {
            return false;
        }

        if ($filters['roll_number'] !== '') {
            $needle = strtolower($filters['roll_number']);
            $hay = strtolower((string) ($row['eligible_roll_number'] ?? '').' '.(string) ($row['roll_display'] ?? ''));

            if (! str_contains($hay, $needle)) {
                return false;
            }
        }

        if ($filters['sangha'] !== '') {
            $needle = strtolower($filters['sangha']);
            $name = strtolower((string) ($row['name'] ?? ''));

            if (! str_contains($name, $needle)) {
                return false;
            }
        }

        if ($filters['exam_id'] !== null && $filters['exam_id'] > 0) {
            if ((int) ($row['exam_id'] ?? 0) !== $filters['exam_id']) {
                return false;
            }
        }

        if ($filters['desk_number'] !== '') {
            $needle = strtolower($filters['desk_number']);
            $desk = strtolower((string) ($row['desk_display'] ?? '').' '.(string) ($row['desk_number'] ?? ''));

            if (! str_contains($desk, $needle)) {
                return false;
            }
        }

        return true;
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

    /**
     * @return array<string, array<int, mixed>>
     */
    private function customFieldRules(Collection $fields, Request $request, ?Sangha $resubmitTarget = null): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = 'custom_fields.'.$field->slug;
            $fieldRules = $field->required ? ['required'] : ['nullable'];

            switch ($field->type) {
                case 'textarea':
                case 'text':
                    $fieldRules[] = 'string';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'time':
                    $fieldRules[] = 'date_format:H:i';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;
                case 'select':
                    $fieldRules[] = 'string';
                    if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_year') {
                        $years = Exam::monasteryExamFormCatalog()['years'];
                        if ($years !== []) {
                            $fieldRules[] = Rule::in($years);
                        } elseif (! empty($field->options) && is_array($field->options)) {
                            $fieldRules[] = Rule::in(array_values($field->options));
                        }
                    } elseif (! empty($field->options) && is_array($field->options)) {
                        $fieldRules[] = Rule::in(array_values($field->options));
                    }
                    break;
                case 'dependent_select':
                    $parentSlug = CustomField::dependentSelectParentSlug($field);
                    if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session') {
                        $fieldRules[] = 'integer';
                        if ($parentSlug) {
                            $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($request, $parentSlug) {
                                if ($value === null || $value === '') {
                                    return;
                                }
                                $year = $request->input('custom_fields.'.$parentSlug);
                                if (! filled($year)) {
                                    $fail(t('validation_exam_year_before_session', 'Choose a year before selecting an exam.'));

                                    return;
                                }
                                $ok = Exam::query()
                                    ->whereKey((int) $value)
                                    ->where('is_active', true)
                                    ->whereYear('exam_date', (int) $year)
                                    ->exists();
                                if (! $ok) {
                                    $fail(t('validation_exam_session_must_match_year', 'The selected exam is not valid for the chosen year.'));
                                }
                            };
                        }
                    } else {
                        $fieldRules[] = 'string';
                        if ($parentSlug) {
                            $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($request, $field, $parentSlug) {
                                if ($value === null || $value === '') {
                                    return;
                                }
                                $year = $request->input('custom_fields.'.$parentSlug);
                                if (! filled($year)) {
                                    $fail(t('validation_exam_year_before_session', 'Choose a year before selecting an exam.'));

                                    return;
                                }
                                $map = is_array($field->options) ? $field->options : [];
                                $allowed = $map[(string) $year] ?? [];
                                if (! is_array($allowed)) {
                                    $allowed = [];
                                }
                                $allowedStr = array_map('strval', $allowed);
                                if (! in_array((string) $value, $allowedStr, true)) {
                                    $fail(t('validation_exam_session_must_match_year', 'The selected exam is not valid for the chosen year.'));
                                }
                            };
                        }
                    }
                    break;
                case 'approved_sangha':
                    $fieldRules[] = 'integer';
                    $monasteryId = (int) Auth::guard('monastery')->id();
                    $fieldRules[] = Rule::exists('sanghas', 'id')->where(
                        fn ($q) => $q->where('monastery_id', $monasteryId)
                            ->where('approved', true)
                    );
                    if ($field->entity_type === 'request') {
                        $programmeScreen = $this->programmeScreenFromRequest($request);
                        $programmeEntityType = $this->programmeEntityType($programmeScreen);
                        if ($programmeEntityType !== null) {
                            $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($programmeEntityType, $monasteryId) {
                                if ($value === null || $value === '') {
                                    return;
                                }
                                $ok = Sangha::query()
                                    ->whereKey((int) $value)
                                    ->where('monastery_id', $monasteryId)
                                    ->where('approved', true)
                                    ->whereExists(function ($sub) use ($programmeEntityType) {
                                        $sub->selectRaw('1')
                                            ->from('custom_field_values')
                                            ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                                            ->where('custom_field_values.entity_type', $programmeEntityType);
                                    })
                                    ->exists();
                                if (! $ok) {
                                    $fail(t('validation_transfer_sangha_programme_mismatch', 'The selected sangha is not in the approved list for this programme.'));
                                }
                            };
                        }
                    }
                    if ($field->entity_type === 'monastery_exam') {
                        $examProgrammeScreen = $this->programmeScreenFromExamTypeId((int) $request->input('exam_type_id'));
                        $examProgrammeEntityType = $this->programmeEntityType($examProgrammeScreen);
                        if ($examProgrammeEntityType !== null) {
                            $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($examProgrammeEntityType, $monasteryId) {
                                if ($value === null || $value === '') {
                                    return;
                                }
                                $ok = Sangha::query()
                                    ->whereKey((int) $value)
                                    ->where('monastery_id', $monasteryId)
                                    ->where('approved', true)
                                    ->whereExists(function ($sub) use ($examProgrammeEntityType) {
                                        $sub->selectRaw('1')
                                            ->from('custom_field_values')
                                            ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                                            ->where('custom_field_values.entity_type', $examProgrammeEntityType);
                                    })
                                    ->exists();
                                if (! $ok) {
                                    $fail(t('validation_transfer_sangha_programme_mismatch', 'The selected sangha is not in the approved list for this programme.'));
                                }
                            };
                        }
                    }
                    break;
                case 'monastery_select':
                    $fieldRules[] = 'integer';
                    $fieldRules[] = Rule::exists('monasteries', 'id')->where(
                        fn ($q) => $q->where('approved', true)->where('is_active', true)
                    );
                    if ($field->entity_type === 'request') {
                        $originId = Auth::guard('monastery')->id();
                        $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($originId) {
                            if ($value === null || $value === '') {
                                return;
                            }
                            if ((int) $value === (int) $originId) {
                                $fail(t('validation_transfer_destination_not_self', 'Choose a monastery other than your own.'));
                            }
                        };
                    }
                    break;
                case 'media':
                    $hasExistingFile = $resubmitTarget && filled($resubmitTarget->getCustomFieldValue($field->slug));
                    if ($field->required && ! $hasExistingFile) {
                        $fieldRules = ['required'];
                    } else {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'image';
                    $fieldRules[] = 'max:5120';
                    break;
                case 'document':
                    $hasExistingFile = $resubmitTarget && filled($resubmitTarget->getCustomFieldValue($field->slug));
                    if ($field->required && ! $hasExistingFile) {
                        $fieldRules = ['required'];
                    } else {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'file';
                    // "Supporting document" should accept common documents and images.
                    $fieldRules[] = 'mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif,webp,bmp,svg,heic,heif';
                    $fieldRules[] = 'max:51200';
                    break;
                case 'video':
                    $hasExistingFile = $resubmitTarget && filled($resubmitTarget->getCustomFieldValue($field->slug));
                    if ($field->required && ! $hasExistingFile) {
                        $fieldRules = ['required'];
                    } else {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm';
                    $fieldRules[] = 'max:51200';
                    break;
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Eligible “View details” mirrors the monastery register form: same built-ins (no roll / no Student Id row),
     * sangha extra custom fields only when no programme is selected, then programme fields.
     *
     * @return array{heading: string, sections: list<array{title: ?string, hint_only?: string, rows?: list<array{label: string, value: string, full_width: bool, link_href: ?string, link_label: ?string}>}>}
     */
    private function buildPortalEligibleSanghaViewDetails(
        Sangha $sangha,
        Collection $sanghaFieldMeta,
        Collection $sanghaCustomFields,
        ?string $programmeContext
    ): array {
        $sections = [];

        $coreRows = [];
        $builtInOrder = ['name', 'father_name', 'nrc_number', 'exam_id', 'description'];
        foreach ($builtInOrder as $slug) {
            if (CustomField::isBuiltInSlugSuppressed('sangha', $slug)) {
                continue;
            }
            $label = (string) ($sanghaFieldMeta->get($slug)?->name ?? $slug);
            $value = match ($slug) {
                'exam_id' => $sangha->exam
                    ? $sangha->exam->name.($sangha->exam->exam_date ? ' ('.$sangha->exam->exam_date->format('M d, Y').')' : '')
                    : '—',
                'name' => $sangha->name !== '' ? $sangha->name : '—',
                'father_name' => filled($sangha->father_name) ? (string) $sangha->father_name : '—',
                'nrc_number' => filled($sangha->nrc_number) ? (string) $sangha->nrc_number : '—',
                'description' => filled($sangha->description) ? (string) $sangha->description : '—',
                default => '—',
            };
            $coreRows[] = [
                'label' => $label,
                'value' => $value,
                'full_width' => $slug === 'description',
                'link_href' => null,
                'link_label' => null,
            ];
        }

        $sections[] = [
            'title' => t('sangha_application_form', 'Sangha Application Form'),
            'rows' => $coreRows,
        ];

        $sections[] = [
            'title' => null,
            'hint_only' => t('sangha_portal_no_student_id_hint', 'Student Id for login is assigned by an administrator after review.'),
            'rows' => [],
        ];

        if (empty($programmeContext) && $sanghaCustomFields->isNotEmpty()) {
            $extraRows = [];
            foreach ($sanghaCustomFields as $cf) {
                $raw = $sangha->getCustomFieldValue($cf->slug);
                $formatted = $this->formatPortalCustomFieldRow($cf, $raw, $sangha);
                $extraRows[] = [
                    'label' => $cf->name,
                    'value' => $formatted['value'],
                    'full_width' => $cf->type === 'textarea',
                    'link_href' => $formatted['link_href'],
                    'link_label' => $formatted['link_label'],
                ];
            }
            if ($extraRows !== []) {
                $sections[] = [
                    'title' => t('custom_fields', 'Custom Fields'),
                    'rows' => $extraRows,
                ];
            }
        }

        $entityTypesToShow = $this->programmeEntityTypesForEligibleDetails($sangha, $programmeContext);
        $canonicalOrder = array_values(self::PORTAL_PROGRAMME_ENTITY_TYPES);
        usort($entityTypesToShow, function (string $a, string $b) use ($canonicalOrder): int {
            $ia = array_search($a, $canonicalOrder, true);
            $ib = array_search($b, $canonicalOrder, true);

            return ($ia === false ? 999 : $ia) <=> ($ib === false ? 999 : $ib);
        });

        $entityLabels = CustomField::entityTypes();
        $map = self::PORTAL_PROGRAMME_ENTITY_TYPES;
        $urlLocksProgramme = is_string($programmeContext) && $programmeContext !== '' && isset($map[$programmeContext]);
        $usePlainProgrammeTitle = $urlLocksProgramme && count($entityTypesToShow) === 1;

        foreach ($entityTypesToShow as $entityType) {
            $fields = CustomField::forEntity($entityType)->get();
            if ($fields->isEmpty()) {
                continue;
            }
            $vals = $this->existingCustomFieldValuesBySlug($sangha, $fields, $entityType);
            $progRows = [];
            foreach ($fields as $cf) {
                $raw = $vals[$cf->slug] ?? null;
                $formatted = $this->formatPortalCustomFieldRow($cf, $raw, $sangha);
                $progRows[] = [
                    'label' => $cf->name,
                    'value' => $formatted['value'],
                    'full_width' => $cf->type === 'textarea',
                    'link_href' => $formatted['link_href'],
                    'link_label' => $formatted['link_label'],
                ];
            }
            $progTitle = ($entityLabels[$entityType] ?? $entityType);
            $sectionTitle = $usePlainProgrammeTitle
                ? t('programme_fields', 'Programme fields')
                : t('programme_fields', 'Programme fields').' — '.$progTitle;
            $sections[] = [
                'title' => $sectionTitle,
                'rows' => $progRows,
            ];
        }

        return [
            'heading' => $sangha->name !== '' ? $sangha->name : '—',
            'sections' => $sections,
        ];
    }

    /**
     * @return list<string> programme_* entity_type values
     */
    private function programmeEntityTypesForEligibleDetails(Sangha $sangha, ?string $programmeContext): array
    {
        $map = self::PORTAL_PROGRAMME_ENTITY_TYPES;
        if (is_string($programmeContext) && $programmeContext !== '' && isset($map[$programmeContext])) {
            return [$map[$programmeContext]];
        }

        $known = array_values($map);

        return CustomFieldValue::query()
            ->where('entity_id', $sangha->id)
            ->whereIn('entity_type', $known)
            ->distinct()
            ->pluck('entity_type')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array{value: string, link_href: ?string, link_label: ?string}
     */
    private function formatPortalCustomFieldRow(CustomField $field, ?string $raw, ?Sangha $sangha = null): array
    {
        if ($raw === null || $raw === '') {
            return ['value' => '—', 'link_href' => null, 'link_label' => null];
        }

        if ($field->type === 'checkbox') {
            $on = in_array(strtolower((string) $raw), ['1', 'true', 'yes', 'on'], true);

            return ['value' => $on ? t('yes', 'Yes') : t('no', 'No'), 'link_href' => null, 'link_label' => null];
        }

        if (in_array($field->type, ['media', 'document', 'video'], true)) {
            $base = basename($raw);
            $href = null;
            if ($sangha !== null) {
                $href = route('monastery.sanghas.custom-field-file', [
                    'sangha' => $sangha->id,
                    'customField' => $field->id,
                ]);
            }

            return ['value' => $base, 'link_href' => $href, 'link_label' => t('open_file', 'Open file')];
        }

        if ($field->type === 'approved_sangha' && ctype_digit((string) $raw)) {
            $other = Sangha::query()->find((int) $raw);

            return ['value' => $other?->name ?? ('#'.$raw), 'link_href' => null, 'link_label' => null];
        }

        if ($field->type === 'monastery_select' && ctype_digit((string) $raw)) {
            $name = Monastery::query()->whereKey((int) $raw)->value('name');

            return ['value' => $name ? (string) $name : ('#'.$raw), 'link_href' => null, 'link_label' => null];
        }

        return ['value' => (string) $raw, 'link_href' => null, 'link_label' => null];
    }
}
