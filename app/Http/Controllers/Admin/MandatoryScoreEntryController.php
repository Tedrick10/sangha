<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Sangha;
use App\Models\Score;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MandatoryScoreEntryController extends Controller
{
    public function index(Request $request): View
    {
        $yearId = $request->filled('year_id') ? (int) $request->year_id : null;
        $years = Exam::query()
            ->whereNotNull('exam_date')
            ->orderByDesc('exam_date')
            ->get(['exam_date'])
            ->map(fn (Exam $exam) => $exam->exam_date?->format('Y'))
            ->filter()
            ->unique()
            ->values();
        $exams = Exam::query()
            ->when($yearId, fn ($q) => $q->whereYear('exam_date', $yearId))
            ->orderByDesc('exam_date')
            ->orderByDesc('id')
            ->get(['id', 'name', 'exam_date']);

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
                    if ($subjects->isEmpty()) {
                        $filterError = t('mandatory_scores_no_subjects', 'This exam has no subjects. Add subjects on the exam edit page.');
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

    public function examOptions(Request $request): JsonResponse
    {
        $yearId = $request->filled('year_id') ? (int) $request->year_id : null;
        $exams = Exam::query()
            ->when($yearId, fn ($q) => $q->whereYear('exam_date', $yearId))
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
        $allowedSubjectIds = $exam->subjects->pluck('id')->all();

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

        $this->persistScoresForCandidate($exam, $sangha, $scoresInput);

        return redirect()
            ->route('admin.mandatory-scores.index', [
                'year_id' => $exam->exam_date?->format('Y'),
                'exam_id' => $exam->id,
                'desk_number' => $validated['desk_number'],
            ])
            ->with('success', t('mandatory_scores_saved', 'Scores saved successfully.'));
    }

    public function grid(Request $request): View
    {
        $yearId = $request->filled('year_id') ? (int) $request->year_id : null;
        $years = Exam::query()
            ->whereNotNull('exam_date')
            ->orderByDesc('exam_date')
            ->get(['exam_date'])
            ->map(fn (Exam $exam) => $exam->exam_date?->format('Y'))
            ->filter()
            ->unique()
            ->values();
        $exams = Exam::query()
            ->when($yearId, fn ($q) => $q->whereYear('exam_date', $yearId))
            ->orderByDesc('exam_date')
            ->orderByDesc('id')
            ->get(['id', 'name', 'exam_date', 'exam_type_id', 'desk_number_prefix']);

        $examId = $request->filled('exam_id') ? (int) $request->exam_id : null;
        $examModel = null;
        $subjects = collect();
        $rows = collect();
        $gridError = null;

        if ($examId !== null) {
            $examModel = Exam::query()
                ->with([
                    'examType:id,name',
                    'subjects' => fn ($q) => $q->where('subjects.is_active', true)->orderBy('subjects.name'),
                ])
                ->find($examId);

            if (! $examModel) {
                $gridError = t('mandatory_scores_exam_not_found', 'Exam not found.');
            } else {
                $subjects = $examModel->subjects;
                if ($subjects->isEmpty()) {
                    $gridError = t('mandatory_scores_no_subjects', 'This exam has no subjects. Add subjects on the exam edit page.');
                } else {
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
                }
            }
        }

        return view('admin.mandatory-scores.grid', compact(
            'yearId',
            'years',
            'exams',
            'examId',
            'examModel',
            'subjects',
            'rows',
            'gridError'
        ));
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

        $sangha = Sangha::query()->findOrFail($validated['sangha_id']);

        if ((int) $sangha->exam_id !== (int) $exam->id || $sangha->desk_number === null) {
            return redirect()
                ->route('admin.mandatory-scores.grid', [
                    'year_id' => $exam->exam_date?->format('Y'),
                    'exam_id' => $exam->id,
                ])
                ->withErrors([
                    'row' => t('mandatory_scores_sangha_mismatch', 'Sangha does not match the selected exam and desk number.'),
                ]);
        }

        $scoresInput = $validated['scores'];
        $filled = collect($scoresInput)->filter(fn ($v) => $v !== null && $v !== '');

        if ($filled->isEmpty()) {
            return redirect()
                ->route('admin.mandatory-scores.grid', [
                    'year_id' => $exam->exam_date?->format('Y'),
                    'exam_id' => $exam->id,
                ])
                ->withErrors([
                    'scores' => t('mandatory_scores_need_one', 'Enter at least one score before saving.'),
                ]);
        }

        $this->persistScoresForCandidate($exam, $sangha, $scoresInput);

        return redirect()
            ->route('admin.mandatory-scores.grid', [
                'year_id' => $exam->exam_date?->format('Y'),
                'exam_id' => $exam->id,
            ])
            ->with('success', t('mandatory_scores_row_saved', 'Scores saved for this candidate.'));
    }

    /**
     * @param  array<string|int, mixed>  $scoresInput
     */
    private function persistScoresForCandidate(Exam $exam, Sangha $sangha, array $scoresInput): void
    {
        $allowedSubjectIds = $exam->subjects->pluck('id')->all();
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
}
