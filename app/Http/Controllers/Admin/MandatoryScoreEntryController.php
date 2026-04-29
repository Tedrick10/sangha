<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Sangha;
use App\Models\Score;
use App\Models\SiteSetting;
use App\Models\TeacherSubjectAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MandatoryScoreEntryController extends Controller
{
    public function index(Request $request): View
    {
        $teacherAllowedExamTypeIds = $this->teacherAllowedExamTypeIds($request);
        $yearId = $request->filled('year_id') ? (int) $request->year_id : null;
        $years = Exam::query()
            ->whereNotNull('exam_date')
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('exam_type_id', $teacherAllowedExamTypeIds))
            ->orderByDesc('exam_date')
            ->get(['exam_date'])
            ->map(fn (Exam $exam) => $exam->exam_date?->format('Y'))
            ->filter()
            ->unique()
            ->values();
        $exams = Exam::query()
            ->when($yearId, fn ($q) => $q->whereYear('exam_date', $yearId))
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('exam_type_id', $teacherAllowedExamTypeIds))
            ->orderByDesc('exam_date')
            ->orderByDesc('id')
            ->get(['id', 'name', 'exam_date', 'exam_type_id']);

        $examId = $request->filled('exam_id') ? (int) $request->exam_id : null;
        $deskNumber = $request->filled('desk_number') ? (int) $request->desk_number : null;

        $examForDeskFilter = $examId ? Exam::query()->find($examId, ['id', 'desk_number_prefix']) : null;

        $deskOptions = collect();
        if ($examId) {
            $deskOptions = Sangha::query()
                ->where('exam_id', $examId)
                ->whereNotNull('desk_number')
                ->orderBy('desk_number')
                ->get(['id', 'desk_number', 'name']);
        }

        $sangha = null;
        $examModel = null;
        $subjects = collect();
        $scoresBySubject = collect();
        $filterError = null;

        if ($examId !== null && $deskNumber !== null) {
            $examModel = Exam::find($examId);
            if (! $examModel) {
                $filterError = t('mandatory_scores_exam_not_found', 'Exam not found.');
            } elseif (! $this->canTeacherAccessExamType($request, (int) $examModel->exam_type_id)) {
                $filterError = t('teacher_scope_exam_denied', 'You are not assigned to this exam type.');
            } else {
                $sangha = Sangha::query()
                    ->where('exam_id', $examId)
                    ->where('desk_number', $deskNumber)
                    ->with('monastery')
                    ->first();

                if (! $sangha) {
                    $filterError = t('mandatory_scores_no_sangha', 'No approved candidate with this desk number for the selected exam.');
                } else {
                    $subjects = $examModel->subjects()->where('subjects.is_active', true)->orderBy('name')->get();
                    $teacherAllowedSubjectIds = $this->teacherAllowedSubjectIds($request, (int) $examModel->exam_type_id);
                    if ($teacherAllowedSubjectIds !== null) {
                        $subjects = $subjects->whereIn('id', $teacherAllowedSubjectIds)->values();
                    }
                    if ($subjects->isEmpty()) {
                        $filterError = t('teacher_scope_subject_denied', 'No subjects are assigned to this teacher for the selected exam type.');
                    } else {
                        $scoresBySubject = Score::query()
                            ->where('sangha_id', $sangha->id)
                            ->where('exam_id', $examModel->id)
                            ->whereIn('subject_id', $subjects->pluck('id'))
                            ->get()
                            ->keyBy('subject_id');
                    }
                }
            }
        }

        return view('admin.mandatory-scores.index', compact(
            'yearId',
            'years',
            'exams',
            'examId',
            'deskNumber',
            'examForDeskFilter',
            'deskOptions',
            'sangha',
            'examModel',
            'subjects',
            'scoresBySubject',
            'filterError'
        ));
    }

    public function yearOptions(Request $request): JsonResponse
    {
        $examTypeId = $request->filled('exam_type_id') ? (int) $request->exam_type_id : null;
        if ($examTypeId < 1) {
            $examTypeId = null;
        }
        if ($examTypeId !== null && ! $this->canTeacherAccessExamType($request, $examTypeId)) {
            return response()->json(['years' => []]);
        }
        $teacherAllowedExamTypeIds = $this->teacherAllowedExamTypeIds($request);
        $years = Exam::query()
            ->whereNotNull('exam_date')
            ->when($examTypeId, fn ($q) => $q->where('exam_type_id', $examTypeId))
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('exam_type_id', $teacherAllowedExamTypeIds))
            ->orderByDesc('exam_date')
            ->get(['exam_date'])
            ->map(fn (Exam $exam) => $exam->exam_date?->format('Y'))
            ->filter()
            ->unique()
            ->values();

        return response()->json(['years' => $years->all()]);
    }

    public function examOptions(Request $request): JsonResponse
    {
        $yearId = $request->filled('year_id') ? (int) $request->year_id : null;
        $examTypeId = $request->filled('exam_type_id') ? (int) $request->exam_type_id : null;
        if ($examTypeId < 1) {
            $examTypeId = null;
        }
        if ($examTypeId !== null && ! $this->canTeacherAccessExamType($request, $examTypeId)) {
            return response()->json(['exams' => []]);
        }
        $teacherAllowedExamTypeIds = $this->teacherAllowedExamTypeIds($request);
        $exams = Exam::query()
            ->when($yearId, fn ($q) => $q->whereYear('exam_date', $yearId))
            ->when($examTypeId, fn ($q) => $q->where('exam_type_id', $examTypeId))
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('exam_type_id', $teacherAllowedExamTypeIds))
            ->orderByDesc('exam_date')
            ->orderByDesc('id')
            ->get(['id', 'name', 'exam_date']);

        return response()->json([
            'exams' => $exams->map(fn (Exam $e) => [
                'id' => $e->id,
                'label' => $e->name.($e->exam_date ? ' ('.$e->exam_date->format('M j, Y').')' : ''),
            ])->values()->all(),
        ]);
    }

    public function deskOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::query()->findOrFail((int) $validated['exam_id']);
        if (! $this->canTeacherAccessExamType($request, (int) $exam->exam_type_id)) {
            abort(403, t('teacher_scope_exam_denied', 'You are not assigned to this exam type.'));
        }

        $desks = Sangha::query()
            ->where('exam_id', $exam->id)
            ->whereNotNull('desk_number')
            ->orderBy('desk_number')
            ->get(['desk_number', 'name']);

        return response()->json([
            'desk_number_prefix' => $exam->desk_number_prefix,
            'desks' => $desks->map(fn (Sangha $s) => [
                'desk_number' => (int) $s->desk_number,
                'name' => $s->name,
            ])->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'sangha_id' => 'required|exists:sanghas,id',
            'desk_number' => 'required|integer|min:1|max:999999',
            'scores' => 'present|array',
            'scores.*' => 'nullable|numeric|min:0',
        ]);

        $exam = Exam::with([
            'subjects' => fn ($q) => $q->where('subjects.is_active', true)->orderBy('name'),
        ])->findOrFail($validated['exam_id']);
        if (! $this->canTeacherAccessExamType($request, (int) $exam->exam_type_id)) {
            return redirect()->back()->withErrors([
                'scores' => t('teacher_scope_exam_denied', 'You are not assigned to this exam type.'),
            ]);
        }
        $allowedSubjectIds = $exam->subjects->pluck('id')->all();
        $teacherAllowedSubjectIds = $this->teacherAllowedSubjectIds($request, (int) $exam->exam_type_id);
        if ($teacherAllowedSubjectIds !== null) {
            $allowedSubjectIds = array_values(array_intersect($allowedSubjectIds, $teacherAllowedSubjectIds));
            if ($allowedSubjectIds === []) {
                return redirect()->back()->withErrors([
                    'scores' => t('teacher_scope_subject_denied', 'No subjects are assigned to this teacher for the selected exam type.'),
                ]);
            }
        }

        $sangha = Sangha::findOrFail($validated['sangha_id']);
        if ((int) $sangha->exam_id !== (int) $exam->id
            || $sangha->desk_number === null
            || (int) $sangha->desk_number !== (int) $validated['desk_number']) {
            return redirect()
                ->route('admin.mandatory-scores.index', [
                    'year_id' => $exam->exam_date?->format('Y'),
                    'exam_id' => $validated['exam_id'],
                    'desk_number' => $validated['desk_number'],
                ])
                ->withInput()
                ->withErrors([
                    'desk_number' => t('mandatory_scores_sangha_mismatch', 'Sangha does not match the selected exam and desk number.'),
                ]);
        }

        $scoresInput = $validated['scores'];
        $scoresInput = collect($scoresInput)
            ->filter(fn ($value, $subjectId) => in_array((int) $subjectId, $allowedSubjectIds, true))
            ->all();
        $filled = collect($scoresInput)->filter(fn ($v) => $v !== null && $v !== '');

        if ($filled->isEmpty()) {
            return redirect()
                ->route('admin.mandatory-scores.index', [
                    'year_id' => $exam->exam_date?->format('Y'),
                    'exam_id' => $validated['exam_id'],
                    'desk_number' => $validated['desk_number'],
                ])
                ->withInput()
                ->withErrors([
                    'scores' => t('mandatory_scores_need_one', 'Enter at least one score before saving.'),
                ]);
        }

        $this->persistScoresForCandidate($exam, $sangha, $scoresInput, $allowedSubjectIds);

        return redirect()
            ->route('admin.mandatory-scores.index', [
                'year_id' => $exam->exam_date?->format('Y'),
                'exam_id' => $exam->id,
                'desk_number' => $validated['desk_number'],
            ])
            ->with('success', t('mandatory_scores_saved', 'Scores saved successfully.'));
    }

    public function moderation(Request $request): View|RedirectResponse
    {
        $examTypes = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get();

        if ($examTypes->isEmpty()) {
            return view('admin.score-moderation.index', array_merge(
                $this->deskGridPageData($request, null),
                ['examTypes' => $examTypes]
            ));
        }

        if (! $request->filled('exam_type_id')) {
            return redirect()->route('admin.score-moderation.index', [
                'exam_type_id' => $examTypes->first()->id,
            ]);
        }

        $examTypeId = (int) $request->exam_type_id;
        if (! $examTypes->firstWhere('id', $examTypeId)) {
            return redirect()->route('admin.score-moderation.index', [
                'exam_type_id' => $examTypes->first()->id,
            ]);
        }

        return view('admin.score-moderation.index', array_merge(
            $this->deskGridPageData($request, $examTypeId),
            ['examTypes' => $examTypes]
        ));
    }

    public function grid(Request $request): View
    {
        return view('admin.mandatory-scores.grid', $this->deskGridPageData($request, null));
    }

    /**
     * @param  ?int  $forcedExamTypeId  When set, desk grid is scoped to this exam type (Score Moderation tabs).
     * @return array<string, mixed>
     */
    private function deskGridPageData(Request $request, ?int $forcedExamTypeId): array
    {
        $teacherAllowedExamTypeIds = $this->teacherAllowedExamTypeIds($request);
        $yearId = $request->filled('year_id') ? (int) $request->year_id : null;
        $examTypeId = $forcedExamTypeId !== null
            ? $forcedExamTypeId
            : ($request->filled('exam_type_id') ? (int) $request->exam_type_id : null);
        if ($examTypeId !== null && $examTypeId < 1) {
            $examTypeId = null;
        }

        $years = Exam::query()
            ->whereNotNull('exam_date')
            ->when($examTypeId, fn ($q) => $q->where('exam_type_id', $examTypeId))
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('exam_type_id', $teacherAllowedExamTypeIds))
            ->orderByDesc('exam_date')
            ->get(['exam_date'])
            ->map(fn (Exam $exam) => $exam->exam_date?->format('Y'))
            ->filter()
            ->unique()
            ->values();
        $exams = Exam::query()
            ->when($yearId, fn ($q) => $q->whereYear('exam_date', $yearId))
            ->when($examTypeId, fn ($q) => $q->where('exam_type_id', $examTypeId))
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('exam_type_id', $teacherAllowedExamTypeIds))
            ->orderByDesc('exam_date')
            ->orderByDesc('id')
            ->get(['id', 'name', 'exam_date', 'exam_type_id', 'desk_number_prefix']);

        $examTypes = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->when($teacherAllowedExamTypeIds !== null, fn ($q) => $q->whereIn('id', $teacherAllowedExamTypeIds))
            ->orderByCanonical()
            ->get();

        $examId = $request->filled('exam_id') ? (int) $request->exam_id : null;
        $examModel = null;
        $subjects = collect();
        $rows = collect();
        $gridError = null;
        $moderationThresholds = [];
        $moderationSummary = collect();
        $moderationStats = null;
        $moderationLocked = false;

        if ($examId !== null) {
            $examModel = Exam::query()
                ->with([
                    'examType:id,name',
                    'subjects' => fn ($q) => $q->where('subjects.is_active', true)->orderBy('subjects.name'),
                ])
                ->find($examId);

            if (! $examModel) {
                $gridError = t('mandatory_scores_exam_not_found', 'Exam not found.');
            } elseif (! $this->canTeacherAccessExamType($request, (int) $examModel->exam_type_id)) {
                $gridError = t('teacher_scope_exam_denied', 'You are not assigned to this exam type.');
                $examModel = null;
            } elseif ($examTypeId !== null && (int) $examModel->exam_type_id !== $examTypeId) {
                $examModel = null;
            } elseif ($yearId !== null && $examModel->exam_date && (int) $examModel->exam_date->format('Y') !== $yearId) {
                $gridError = t('mandatory_scores_grid_filter_mismatch', 'The selected exam does not match the year filter.');
                $examModel = null;
            } else {
                $subjects = $examModel->subjects;
                $teacherAllowedSubjectIds = $this->teacherAllowedSubjectIds($request, (int) $examModel->exam_type_id);
                if ($teacherAllowedSubjectIds !== null) {
                    $subjects = $subjects->whereIn('id', $teacherAllowedSubjectIds)->values();
                }
                if ($subjects->isEmpty()) {
                    $gridError = t('teacher_scope_subject_denied', 'No subjects are assigned to this teacher for the selected exam type.');
                } else {
                    $thresholdRaw = SiteSetting::get($this->moderationThresholdKey((int) $examModel->id));
                    $thresholdJson = $thresholdRaw ? json_decode($thresholdRaw, true) : [];
                    $moderationThresholds = is_array($thresholdJson) ? $thresholdJson : [];
                    $moderationLocked = SiteSetting::get($this->moderationLockKey((int) $examModel->id)) === '1';

                    $sanghas = Sangha::query()
                        ->where('exam_id', $examModel->id)
                        ->whereNotNull('desk_number')
                        ->orderBy('desk_number')
                        ->get();

                    $subjectIds = $subjects->pluck('id')->all();
                    $sanghaIds = $sanghas->pluck('id')->all();

                    $scoresGrouped = collect();
                    if ($sanghaIds !== []) {
                        $scoresGrouped = Score::query()
                            ->where('exam_id', $examModel->id)
                            ->whereIn('subject_id', $subjectIds)
                            ->whereIn('sangha_id', $sanghaIds)
                            ->get()
                            ->groupBy('sangha_id')
                            ->map(fn ($group) => $group->keyBy('subject_id'));
                    }

                    foreach ($sanghas as $sangha) {
                        $rows->push([
                            'sangha' => $sangha,
                            'scoresBySubject' => $scoresGrouped->get($sangha->id, collect()),
                        ]);
                    }

                    $moderationSummary = $subjects
                        ->map(function ($subject) use ($moderationThresholds) {
                            $sid = (int) $subject->id;
                            $threshold = $moderationThresholds[$sid] ?? $moderationThresholds[(string) $sid] ?? null;
                            if ($threshold === null || $threshold === '') {
                                return null;
                            }

                            return [
                                'subject' => $subject->name,
                                'threshold' => (float) $threshold,
                            ];
                        })
                        ->filter()
                        ->values();

                    if ($moderationSummary->isNotEmpty()) {
                        $passCount = 0;
                        $failCount = 0;

                        foreach ($rows as $row) {
                            $scoresBySubject = $row['scoresBySubject'];
                            $isFail = false;
                            foreach ($subjects as $subject) {
                                $sid = (int) $subject->id;
                                $threshold = $moderationThresholds[$sid] ?? $moderationThresholds[(string) $sid] ?? null;
                                if ($threshold === null || $threshold === '') {
                                    continue;
                                }
                                $scoreRow = $scoresBySubject->get($sid);
                                $value = $scoreRow?->value;
                                if ($value === null || (float) $value < (float) $threshold) {
                                    $isFail = true;
                                    break;
                                }
                            }

                            if ($isFail) {
                                $failCount++;
                            } else {
                                $passCount++;
                            }
                        }

                        $moderationStats = [
                            'pass' => $passCount,
                            'fail' => $failCount,
                            'total' => $rows->count(),
                        ];
                    }
                }
            }
        }

        return compact(
            'yearId',
            'examTypeId',
            'examTypes',
            'years',
            'exams',
            'examId',
            'examModel',
            'subjects',
            'rows',
            'gridError',
            'moderationThresholds',
            'moderationSummary',
            'moderationStats',
            'moderationLocked'
        );
    }

    public function storeGridRow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'sangha_id' => 'required|exists:sanghas,id',
            'scores' => 'present|array',
            'scores.*' => 'nullable|numeric|min:0',
        ]);

        $exam = Exam::with([
            'subjects' => fn ($q) => $q->where('subjects.is_active', true)->orderBy('subjects.name'),
        ])->findOrFail($validated['exam_id']);
        if (! $this->canTeacherAccessExamType($request, (int) $exam->exam_type_id)) {
            return redirect()->back()->withErrors([
                'scores' => t('teacher_scope_exam_denied', 'You are not assigned to this exam type.'),
            ]);
        }
        $allowedSubjectIds = $exam->subjects->pluck('id')->all();
        $teacherAllowedSubjectIds = $this->teacherAllowedSubjectIds($request, (int) $exam->exam_type_id);
        if ($teacherAllowedSubjectIds !== null) {
            $allowedSubjectIds = array_values(array_intersect($allowedSubjectIds, $teacherAllowedSubjectIds));
            if ($allowedSubjectIds === []) {
                return redirect()->back()->withErrors([
                    'scores' => t('teacher_scope_subject_denied', 'No subjects are assigned to this teacher for the selected exam type.'),
                ]);
            }
        }

        $sangha = Sangha::query()->findOrFail($validated['sangha_id']);

        if ((int) $sangha->exam_id !== (int) $exam->id || $sangha->desk_number === null) {
            return redirect()
                ->route('admin.mandatory-scores.grid', array_filter([
                    'year_id' => $exam->exam_date?->format('Y'),
                    'exam_type_id' => $request->filled('exam_type_id') ? (int) $request->exam_type_id : null,
                    'exam_id' => $exam->id,
                ], fn ($v) => $v !== null && $v !== ''))
                ->withErrors([
                    'row' => t('mandatory_scores_sangha_mismatch', 'Sangha does not match the selected exam and desk number.'),
                ]);
        }

        $scoresInput = $validated['scores'];
        $scoresInput = collect($scoresInput)
            ->filter(fn ($value, $subjectId) => in_array((int) $subjectId, $allowedSubjectIds, true))
            ->all();
        $filled = collect($scoresInput)->filter(fn ($v) => $v !== null && $v !== '');

        if ($filled->isEmpty()) {
            return redirect()
                ->route('admin.mandatory-scores.grid', array_filter([
                    'year_id' => $exam->exam_date?->format('Y'),
                    'exam_type_id' => $request->filled('exam_type_id') ? (int) $request->exam_type_id : null,
                    'exam_id' => $exam->id,
                ], fn ($v) => $v !== null && $v !== ''))
                ->withErrors([
                    'scores' => t('mandatory_scores_need_one', 'Enter at least one score before saving.'),
                ]);
        }

        $this->persistScoresForCandidate($exam, $sangha, $scoresInput, $allowedSubjectIds);

        return redirect()
            ->route('admin.mandatory-scores.grid', array_filter([
                'year_id' => $exam->exam_date?->format('Y'),
                'exam_type_id' => $request->filled('exam_type_id') ? (int) $request->exam_type_id : null,
                'exam_id' => $exam->id,
            ], fn ($v) => $v !== null && $v !== ''))
            ->with('success', t('mandatory_scores_row_saved', 'Scores saved for this candidate.'));
    }

    public function storeModerationControl(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'scores' => 'present|array',
            'scores.*' => 'nullable|numeric|min:0',
            'submit_action' => 'required|in:save,confirm',
            'exam_type_id' => 'nullable|integer|min:1',
            'year_id' => 'nullable|integer',
        ]);

        $exam = Exam::with([
            'subjects' => fn ($q) => $q->where('subjects.is_active', true)->orderBy('subjects.name'),
        ])->findOrFail((int) $validated['exam_id']);

        $scoresInput = $validated['scores'];
        $filled = collect($scoresInput)->filter(fn ($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return redirect()
                ->route('admin.score-moderation.index', array_filter([
                    'exam_type_id' => $request->filled('exam_type_id') ? (int) $request->exam_type_id : null,
                    'year_id' => $request->filled('year_id') ? (int) $request->year_id : null,
                    'exam_id' => $exam->id,
                ], fn ($v) => $v !== null && $v !== ''))
                ->withErrors([
                    'scores' => t('mandatory_scores_need_one', 'Enter at least one score before saving.'),
                ]);
        }

        $thresholds = [];
        foreach ($scoresInput as $subjectIdStr => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $thresholds[(int) $subjectIdStr] = (float) $value;
        }

        SiteSetting::set(
            $this->moderationThresholdKey((int) $exam->id),
            json_encode($thresholds, JSON_UNESCAPED_UNICODE)
        );
        SiteSetting::set($this->moderationLockKey((int) $exam->id), '0');

        if (($validated['submit_action'] ?? '') === 'confirm') {
            $subjectIds = $exam->subjects->pluck('id')->map(fn ($id) => (int) $id)->all();
            $sanghaIds = Sangha::query()
                ->where('exam_id', $exam->id)
                ->whereNotNull('desk_number')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $scoresGrouped = collect();
            if ($sanghaIds !== [] && $subjectIds !== []) {
                $scoresGrouped = Score::query()
                    ->where('exam_id', $exam->id)
                    ->whereIn('subject_id', $subjectIds)
                    ->whereIn('sangha_id', $sanghaIds)
                    ->get()
                    ->groupBy('sangha_id')
                    ->map(fn ($group) => $group->keyBy('subject_id'));
            }

            foreach ($sanghaIds as $sanghaId) {
                $bySubject = $scoresGrouped->get($sanghaId, collect());
                $isFail = false;
                foreach ($thresholds as $subjectId => $threshold) {
                    $score = $bySubject->get((int) $subjectId);
                    $value = $score?->value;
                    if ($value === null || (float) $value < (float) $threshold) {
                        $isFail = true;
                        break;
                    }
                }

                Score::query()
                    ->where('exam_id', $exam->id)
                    ->where('sangha_id', $sanghaId)
                    ->update(['moderation_decision' => $isFail ? 'fail' : 'pass']);
            }

            SiteSetting::set($this->moderationLockKey((int) $exam->id), '1');
            $this->syncPublishedPassSnapshot();
        }

        return redirect()
            ->route('admin.score-moderation.index', array_filter([
                'exam_type_id' => $request->filled('exam_type_id') ? (int) $request->exam_type_id : null,
                'year_id' => $request->filled('year_id') ? (int) $request->year_id : null,
                'exam_id' => $exam->id,
            ], fn ($v) => $v !== null && $v !== ''))
            ->with(
                'success',
                ($validated['submit_action'] ?? '') === 'confirm'
                    ? t('score_moderation_confirmed', 'Moderation marks saved and confirmed.')
                    : t('score_moderation_saved', 'Moderation marks saved.')
            );
    }

    private function moderationThresholdKey(int $examId): string
    {
        return 'moderation_thresholds_exam_'.$examId;
    }

    private function moderationLockKey(int $examId): string
    {
        return 'moderation_locked_exam_'.$examId;
    }

    private function syncPublishedPassSnapshot(): void
    {
        $raw = SiteSetting::get('pass_sanghas_snapshot');
        if (! $raw) {
            return;
        }

        $snapshot = json_decode($raw, true);
        if (! is_array($snapshot)) {
            return;
        }

        $passIds = Sangha::query()
            ->where(function ($query) {
                $query->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->whereColumn('scores.exam_id', 'sanghas.exam_id')
                        ->where('scores.moderation_decision', 'fail');
                })->where(function ($inner) {
                    $inner->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('scores')
                            ->join('subjects', 'subjects.id', '=', 'scores.subject_id')
                            ->whereColumn('scores.sangha_id', 'sanghas.id')
                            ->whereColumn('scores.exam_id', 'sanghas.exam_id')
                            ->whereNotNull('subjects.pass_mark')
                            ->whereRaw('scores.value >= subjects.pass_mark');
                    })->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('scores')
                            ->whereColumn('scores.sangha_id', 'sanghas.id')
                            ->whereColumn('scores.exam_id', 'sanghas.exam_id')
                            ->where('scores.moderation_decision', 'pass');
                    });
                });
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $passIdSet = array_fill_keys($passIds, true);
        $rows = collect($snapshot['pass_sanghas'] ?? [])
            ->filter(function ($row) use ($passIdSet) {
                $id = (int) ($row['id'] ?? 0);

                return $id > 0 && isset($passIdSet[$id]);
            })
            ->values()
            ->all();

        $snapshot['pass_sanghas'] = $rows;
        SiteSetting::set('pass_sanghas_snapshot', json_encode($snapshot, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param  array<string|int, mixed>  $scoresInput
     */
    private function persistScoresForCandidate(Exam $exam, Sangha $sangha, array $scoresInput, ?array $forcedAllowedSubjectIds = null): void
    {
        $allowedSubjectIds = $forcedAllowedSubjectIds ?? $exam->subjects->pluck('id')->all();
        $deskForScore = $sangha->desk_number !== null && $sangha->desk_number !== ''
            ? (string) $sangha->desk_number
            : null;

        DB::transaction(function () use ($scoresInput, $allowedSubjectIds, $sangha, $exam, $deskForScore) {
            foreach ($scoresInput as $subjectIdStr => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                $subjectId = (int) $subjectIdStr;
                if (! in_array($subjectId, $allowedSubjectIds, true)) {
                    continue;
                }

                Score::query()->updateOrCreate(
                    [
                        'sangha_id' => $sangha->id,
                        'subject_id' => $subjectId,
                        'exam_id' => $exam->id,
                    ],
                    [
                        'value' => (float) $value,
                        'desk_number' => $deskForScore,
                        'father_name' => $sangha->father_name,
                        'nrc_number' => $sangha->nrc_number,
                        'candidate_ref' => $sangha->username,
                    ]
                );
            }
        });
    }

    private function teacherAllowedExamTypeIds(Request $request): ?array
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }
        $user->loadMissing('role');
        if (! $user->isTeacher()) {
            return null;
        }

        return TeacherSubjectAssignment::query()
            ->where('user_id', $user->id)
            ->distinct()
            ->pluck('exam_type_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function teacherAllowedSubjectIds(Request $request, int $examTypeId): ?array
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }
        $user->loadMissing('role');
        if (! $user->isTeacher()) {
            return null;
        }

        return TeacherSubjectAssignment::query()
            ->where('user_id', $user->id)
            ->where('exam_type_id', $examTypeId)
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function canTeacherAccessExamType(Request $request, int $examTypeId): bool
    {
        $allowed = $this->teacherAllowedExamTypeIds($request);
        if ($allowed === null) {
            return true;
        }

        return in_array($examTypeId, $allowed, true);
    }
}
