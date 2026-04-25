<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Sangha;
use App\Models\Score;
use App\Models\SiteSetting;
use App\Models\Subject;
use App\Support\MonasteryPortalResultsSnapshot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\View\View;

class ScoreController extends Controller
{
    public function index(Request $request): View
    {
        $screen = in_array($request->get('screen'), ['all', 'top20', 'moderation', 'pass', 'fail'], true)
            ? $request->get('screen')
            : 'all';

        $topSanghas = collect();

        if ($screen === 'top20') {
            $aggregateSub = $this->scoresPerSanghaAggregateSubquery($request);

            $topSanghas = Sangha::query()
                ->with(['monastery', 'exam'])
                ->joinSub($aggregateSub, 'score_agg', 'sanghas.id', '=', 'score_agg.sangha_id')
                ->select('sanghas.*', 'score_agg.total_score', 'score_agg.average_score', 'score_agg.score_count')
                ->when($request->filled('sangha_id'), fn ($query) => $query->where('sanghas.id', $request->sangha_id))
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('sanghas.name', 'like', "%{$search}%")
                            ->orWhere('sanghas.username', 'like', "%{$search}%")
                            ->orWhereExists(function ($sub) use ($search) {
                                $sub->select(DB::raw(1))
                                    ->from('scores')
                                    ->whereColumn('scores.sangha_id', 'sanghas.id')
                                    ->where(function ($s) use ($search) {
                                        $s->where('scores.father_name', 'like', "%{$search}%")
                                            ->orWhere('scores.nrc_number', 'like', "%{$search}%")
                                            ->orWhere('scores.candidate_ref', 'like', "%{$search}%");
                                    });
                            });
                    });
                })
                ->orderByDesc('score_agg.total_score')
                ->orderByDesc('score_agg.average_score')
                ->limit(20)
                ->get();

            $topSanghas = $topSanghas
                ->values()
                ->map(fn ($sangha, $index) => ['sangha' => $sangha, 'default_order' => $index])
                ->sortBy(function (array $entry) {
                    return [
                        is_null($entry['sangha']->top20_position) ? 1 : 0,
                        $entry['sangha']->top20_position ?? 9999,
                        $entry['default_order'],
                    ];
                })
                ->pluck('sangha')
                ->values();

            $metaBySanghaId = Score::latestScoreRowMetaBySanghaIds($topSanghas->pluck('id')->all());
            $topSanghas = $topSanghas->map(function ($sangha) use ($metaBySanghaId) {
                $row = $metaBySanghaId->get($sangha->id, [
                    'father_name' => null,
                    'nrc_number' => null,
                    'candidate_ref' => null,
                    'desk_number' => null,
                ]);
                $sangha->latest_score_father_name = $row['father_name'];
                $sangha->latest_score_nrc_number = $row['nrc_number'];
                $sangha->latest_score_candidate_ref = $row['candidate_ref'];
                $sangha->latest_score_desk_number = $row['desk_number'];

                return $sangha;
            });
        }

        $query = Score::with(['sangha.monastery', 'sangha.exam', 'subject', 'exam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->whereHas('sangha', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhere('scores.father_name', 'like', "%{$search}%")
                    ->orWhere('scores.nrc_number', 'like', "%{$search}%")
                    ->orWhere('scores.candidate_ref', 'like', "%{$search}%");
            });
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('sangha_id')) {
            $query->where('sangha_id', $request->sangha_id);
        }
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($screen === 'moderation') {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('subjects')
                    ->whereColumn('subjects.id', 'scores.subject_id')
                    ->whereNotNull('subjects.moderation_mark')
                    ->whereNotNull('subjects.pass_mark')
                    ->whereRaw('scores.value >= subjects.moderation_mark')
                    ->whereRaw('scores.value < subjects.pass_mark');
            })->whereNull('scores.moderation_decision');
        } elseif ($screen === 'pass') {
            $query->where(function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('subjects')
                        ->whereColumn('subjects.id', 'scores.subject_id')
                        ->whereNotNull('subjects.pass_mark')
                        ->whereRaw('scores.value >= subjects.pass_mark');
                })->orWhere('scores.moderation_decision', 'pass');
            });
        } elseif ($screen === 'fail') {
            $query->where(function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('subjects')
                        ->whereColumn('subjects.id', 'scores.subject_id')
                        ->whereNotNull('subjects.moderation_mark')
                        ->whereRaw('scores.value < subjects.moderation_mark');
                })->orWhere('scores.moderation_decision', 'fail');
            });
        }

        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';
        if ($sort === 'sangha') {
            $query->join('sanghas', 'scores.sangha_id', '=', 'sanghas.id')
                ->orderBy('sanghas.name', $order)
                ->select('scores.*');
        } elseif ($sort === 'subject') {
            $query->join('subjects', 'scores.subject_id', '=', 'subjects.id')
                ->orderBy('subjects.name', $order)
                ->select('scores.*');
        } elseif ($sort === 'exam') {
            $query->leftJoin('exams', 'scores.exam_id', '=', 'exams.id')
                ->orderBy('exams.name', $order)
                ->select('scores.*');
        } elseif (in_array($sort, ['value', 'created_at'])) {
            $query->orderBy('scores.'.$sort, $order);
        } else {
            $query->latest();
        }
        $scores = $query->paginate(admin_per_page(15))->withQueryString();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sanghas = Sangha::with('monastery')->orderBy('name')->get();
        $exams = $this->examsForScoreFilters();

        return view('admin.scores.index', compact('scores', 'subjects', 'sanghas', 'exams', 'screen', 'topSanghas'));
    }

    public function create(Request $request): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sanghas = Sangha::with('monastery')->orderBy('name')->get();
        $exams = $this->examsEligibleForScoreEntry();

        return view('admin.scores.create', compact('subjects', 'sanghas', 'exams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'desk_number' => $this->normalizeScoreDeskNumberInput($request->input('desk_number')),
        ]);

        $validated = $request->validate([
            'sangha_id' => 'required|exists:sanghas,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => ['nullable', $this->examIdEligibleForScoreEntryRule()],
            'desk_number' => 'nullable|string|max:120',
            'value' => 'required|numeric|min:0',
            'moderation_decision' => 'nullable|in:pass,fail',
            'father_name' => 'nullable|string|max:255',
            'nrc_number' => 'nullable|string|max:100',
            'candidate_ref' => 'nullable|string|max:120',
        ]);

        $examId = isset($validated['exam_id']) && $validated['exam_id'] !== '' && $validated['exam_id'] !== null
            ? (int) $validated['exam_id']
            : null;

        $validated['desk_number'] = $this->stripDeskPrefixForStorage(
            $validated['desk_number'] ?? null,
            $examId,
            (int) $validated['sangha_id']
        );

        if ($validated['desk_number'] !== null && mb_strlen($validated['desk_number']) > 64) {
            return back()
                ->withInput()
                ->withErrors(['desk_number' => 'Desk number must be 64 characters or fewer.']);
        }

        if ($this->scoreTripletExists((int) $validated['sangha_id'], (int) $validated['subject_id'], $examId, null)) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => t('score_duplicate_triplet', 'A score for this sangha, subject, and exam already exists.')]);
        }

        try {
            Score::create(array_merge($validated, ['exam_id' => $examId]));
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
                return back()
                    ->withInput()
                    ->withErrors(['subject_id' => t('score_duplicate_triplet', 'A score for this sangha, subject, and exam already exists.')]);
            }
            throw $e;
        }

        return redirect()->route('admin.scores.index')->with('success', 'Score created successfully.');
    }

    public function edit(Score $score): View
    {
        $score->loadMissing(['exam', 'sangha.exam']);
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sanghas = Sangha::with('monastery')->orderBy('name')->get();
        $exams = $this->examsEligibleForScoreEntry($score->exam_id);
        $deskNumberEditValue = $this->deskNumberForEditForm($score);

        return view('admin.scores.edit', compact('score', 'subjects', 'sanghas', 'exams', 'deskNumberEditValue'));
    }

    public function update(Request $request, Score $score): RedirectResponse
    {
        $request->merge([
            'desk_number' => $this->normalizeScoreDeskNumberInput($request->input('desk_number')),
        ]);

        $validated = $request->validate([
            'sangha_id' => 'required|exists:sanghas,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => ['nullable', $this->examIdEligibleForScoreEntryRule()],
            'desk_number' => 'nullable|string|max:120',
            'value' => 'required|numeric|min:0',
            'moderation_decision' => 'nullable|in:pass,fail',
            'father_name' => 'nullable|string|max:255',
            'nrc_number' => 'nullable|string|max:100',
            'candidate_ref' => 'nullable|string|max:120',
        ]);

        $examId = isset($validated['exam_id']) && $validated['exam_id'] !== '' && $validated['exam_id'] !== null
            ? (int) $validated['exam_id']
            : null;

        $validated['desk_number'] = $this->stripDeskPrefixForStorage(
            $validated['desk_number'] ?? null,
            $examId,
            (int) $validated['sangha_id']
        );

        if ($validated['desk_number'] !== null && mb_strlen($validated['desk_number']) > 64) {
            return back()
                ->withInput()
                ->withErrors(['desk_number' => 'Desk number must be 64 characters or fewer.']);
        }

        if ($this->scoreTripletExists((int) $validated['sangha_id'], (int) $validated['subject_id'], $examId, $score->id)) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => t('score_duplicate_triplet', 'A score for this sangha, subject, and exam already exists.')]);
        }

        try {
            $score->update(array_merge($validated, ['exam_id' => $examId]));
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
                return back()
                    ->withInput()
                    ->withErrors(['subject_id' => t('score_duplicate_triplet', 'A score for this sangha, subject, and exam already exists.')]);
            }
            throw $e;
        }

        if (in_array($validated['moderation_decision'] ?? null, ['pass', 'fail'], true)) {
            return redirect()
                ->route('admin.scores.index', ['screen' => $validated['moderation_decision']])
                ->with('success', 'Score updated successfully.');
        }

        return redirect()->route('admin.scores.index')->with('success', 'Score updated successfully.');
    }

    public function destroy(Score $score): RedirectResponse
    {
        $score->delete();

        return redirect()->route('admin.scores.index')->with('success', 'Score deleted successfully.');
    }

    public function updateDecision(Request $request, Score $score): RedirectResponse
    {
        $validated = $request->validate([
            'moderation_decision' => 'required|in:pass,fail',
        ]);

        $score->update([
            'moderation_decision' => $validated['moderation_decision'],
        ]);

        $targetScreen = $validated['moderation_decision'] === 'pass' ? 'pass' : 'fail';

        return redirect()
            ->route('admin.scores.index', ['screen' => $targetScreen])
            ->with('success', 'Moderation decision saved.');
    }

    public function reorderTop20(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sangha_ids' => 'required|array|min:1|max:20',
            'sangha_ids.*' => 'integer|exists:sanghas,id',
        ]);

        $ids = collect($validated['sangha_ids'])->unique()->values()->all();

        DB::transaction(function () use ($ids) {
            Sangha::whereNotNull('top20_position')->update(['top20_position' => null]);
            foreach ($ids as $index => $id) {
                Sangha::whereKey($id)->update(['top20_position' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Top 20 order saved.']);
    }

    public function generatePassList(): JsonResponse
    {
        $generatedAt = now()->toDateTimeString();

        $portalSnapshot = MonasteryPortalResultsSnapshot::build($generatedAt);
        SiteSetting::set(
            MonasteryPortalResultsSnapshot::key(),
            json_encode($portalSnapshot, JSON_UNESCAPED_UNICODE)
        );

        $topSanghas = Sangha::query()
            ->joinSub($this->scoresPerSanghaAggregateSubquery(new Request), 'score_agg', 'sanghas.id', '=', 'score_agg.sangha_id')
            ->select('sanghas.*', 'score_agg.total_score', 'score_agg.average_score', 'score_agg.score_count')
            ->orderByDesc('score_agg.total_score')
            ->orderByDesc('score_agg.average_score')
            ->limit(20)
            ->get()
            ->values()
            ->map(fn ($sangha, $index) => ['sangha' => $sangha, 'default_order' => $index])
            ->sortBy(function (array $entry) {
                return [
                    is_null($entry['sangha']->top20_position) ? 1 : 0,
                    $entry['sangha']->top20_position ?? 9999,
                    $entry['default_order'],
                ];
            })
            ->pluck('sangha')
            ->values();

        $top20Ids = $topSanghas->pluck('id')->all();

        $allPassSanghas = Sangha::query()
            ->leftJoin('scores', 'scores.sangha_id', '=', 'sanghas.id')
            ->with('monastery')
            ->where(function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->join('subjects', 'subjects.id', '=', 'scores.subject_id')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->whereNotNull('subjects.pass_mark')
                        ->whereRaw('scores.value >= subjects.pass_mark');
                })->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->where('scores.moderation_decision', 'pass');
                });
            })
            ->select('sanghas.*')
            ->distinct()
            ->get();

        $passById = $allPassSanghas->keyBy('id');

        $orderedTopPassSanghas = collect($top20Ids)
            ->map(fn ($id) => $passById->get($id))
            ->filter()
            ->values();

        $orderedTopPassIds = $orderedTopPassSanghas->pluck('id')->all();

        $remainingPassSanghas = $allPassSanghas
            ->reject(fn ($sangha) => in_array($sangha->id, $orderedTopPassIds, true))
            ->sortBy('name')
            ->values();

        $passSanghas = $orderedTopPassSanghas
            ->concat($remainingPassSanghas)
            ->values();

        $passSanghas = (new EloquentCollection($passSanghas->all()))
            ->loadMissing(['exam', 'monastery']);

        $metaBySanghaId = Score::latestScoreRowMetaBySanghaIds($passSanghas->pluck('id')->all());

        $passSanghaIds = $passSanghas->pluck('id')->all();
        $latestScoreIdRows = Score::query()
            ->selectRaw('MAX(id) as id')
            ->whereIn('sangha_id', $passSanghaIds)
            ->groupBy('sangha_id')
            ->pluck('id');
        $latestScoresBySanghaId = $latestScoreIdRows->isEmpty()
            ? collect()
            : Score::query()
                ->whereIn('id', $latestScoreIdRows)
                ->with('exam')
                ->get()
                ->keyBy('sangha_id');

        $snapshot = [
            'generated_at' => $generatedAt,
            'pass_sanghas' => $passSanghas->map(function ($sangha) use ($metaBySanghaId, $latestScoresBySanghaId) {
                $row = $metaBySanghaId->get($sangha->id, [
                    'father_name' => null,
                    'nrc_number' => null,
                    'candidate_ref' => null,
                    'desk_number' => null,
                ]);

                $deskRaw = $row['desk_number'];
                $latestScore = $latestScoresBySanghaId->get($sangha->id);
                $deskPrefix = $latestScore?->exam?->desk_number_prefix ?? $sangha->exam?->desk_number_prefix ?? '';
                $deskDisplay = ($deskRaw !== null && $deskRaw !== '')
                    ? $deskPrefix.$deskRaw
                    : null;

                return [
                    'id' => $sangha->id,
                    'name' => $sangha->name,
                    'monastery_name' => $sangha->monastery->name ?? '—',
                    'father_name' => $row['father_name'],
                    'nrc_number' => $row['nrc_number'],
                    'candidate_ref' => $row['candidate_ref'],
                    'desk_number' => $deskDisplay,
                ];
            })->values()->all(),
        ];

        SiteSetting::set('pass_sanghas_snapshot', json_encode($snapshot, JSON_UNESCAPED_UNICODE));

        return response()->json([
            'message' => 'Published public pass list and monastery portal results.',
            'generated_at' => $snapshot['generated_at'],
        ]);
    }

    /**
     * Per-sangha aggregates for Top 20. Uses a subquery so MySQL/MariaDB ONLY_FULL_GROUP_BY does not reject
     * SELECT sanghas.* with JOIN scores + GROUP BY sanghas.id.
     */
    protected function scoresPerSanghaAggregateSubquery(?Request $filterRequest = null): Builder
    {
        $filterRequest ??= request();

        return Score::query()
            ->select('scores.sangha_id')
            ->selectRaw('COALESCE(SUM(scores.value), 0) as total_score')
            ->selectRaw('COALESCE(AVG(scores.value), 0) as average_score')
            ->selectRaw('COUNT(scores.id) as score_count')
            ->when($filterRequest->filled('subject_id'), fn ($query) => $query->where('scores.subject_id', $filterRequest->subject_id))
            ->when($filterRequest->filled('exam_id'), fn ($query) => $query->where('scores.exam_id', $filterRequest->exam_id))
            ->groupBy('scores.sangha_id')
            ->havingRaw('COUNT(scores.id) > 0');
    }

    /**
     * Exams for the scores index filters — all exams so historical rows remain searchable.
     */
    private function examsForScoreFilters(): EloquentCollection
    {
        return Exam::query()->orderByDesc('exam_date')->get();
    }

    /**
     * Exams allowed for score entry: no date, or exam date is today or in the past.
     * When editing, pass the current row's exam id so it stays selectable even if mis-dated.
     */
    private function examsEligibleForScoreEntry(?int $alwaysIncludeExamId = null): EloquentCollection
    {
        return Exam::query()
            ->where(function ($q) use ($alwaysIncludeExamId) {
                $q->where(function ($inner) {
                    $inner->whereNull('exam_date')
                        ->orWhereDate('exam_date', '<=', now());
                });
                if ($alwaysIncludeExamId !== null) {
                    $q->orWhere('id', $alwaysIncludeExamId);
                }
            })
            ->orderByDesc('exam_date')
            ->get();
    }

    /**
     * Rejects exam_id pointing at a future-dated exam (score entry only after the exam date).
     */
    private function examIdEligibleForScoreEntryRule(): Exists
    {
        return Rule::exists('exams', 'id')->where(function ($query) {
            $query->where(function ($q) {
                $q->whereNull('exam_date')
                    ->orWhereDate('exam_date', '<=', now());
            });
        });
    }

    private function normalizeScoreDeskNumberInput(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = is_string($value) ? $value : (string) $value;
        $s = trim($s);

        return $s === '' ? null : $s;
    }

    /**
     * Show exam desk prefix + stored value in the edit form (same as list view).
     */
    private function deskNumberForEditForm(Score $score): string
    {
        $score->loadMissing(['exam', 'sangha.exam']);
        $prefix = $score->exam?->desk_number_prefix ?? $score->sangha->exam?->desk_number_prefix ?? '';
        $raw = $score->desk_number;
        if ($raw === null || $raw === '') {
            return '';
        }
        $rawStr = (string) $raw;
        if ($prefix !== '' && ! str_starts_with($rawStr, $prefix)) {
            return $prefix.$rawStr;
        }

        return $rawStr;
    }

    /**
     * Remove exam desk prefix before persisting so the DB stores the same suffix as mandatory entry / list logic.
     */
    private function stripDeskPrefixForStorage(?string $desk, ?int $examId, int $sanghaId): ?string
    {
        if ($desk === null || $desk === '') {
            return null;
        }
        $exam = $examId !== null ? Exam::query()->find($examId) : null;
        if (! $exam) {
            $exam = Sangha::query()->whereKey($sanghaId)->with('exam')->first()?->exam;
        }
        $prefix = $exam?->desk_number_prefix ?? '';
        if ($prefix === '' || ! str_starts_with($desk, $prefix)) {
            return $desk;
        }
        $rest = substr($desk, strlen($prefix));
        $rest = trim($rest);

        return $rest === '' ? null : $rest;
    }

    private function scoreTripletExists(int $sanghaId, int $subjectId, ?int $examId, ?int $ignoreScoreId): bool
    {
        $q = Score::query()
            ->where('sangha_id', $sanghaId)
            ->where('subject_id', $subjectId);
        if ($examId === null) {
            $q->whereNull('exam_id');
        } else {
            $q->where('exam_id', $examId);
        }
        if ($ignoreScoreId !== null) {
            $q->where('id', '!=', $ignoreScoreId);
        }

        return $q->exists();
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $msg = $e->getMessage();

        return str_contains($msg, 'scores_sangha_subject_exam_unique')
            || str_contains($msg, 'UNIQUE constraint failed')
            || (str_contains($msg, 'Duplicate') && str_contains($msg, 'scores'));
    }
}
