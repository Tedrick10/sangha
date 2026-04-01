<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Sangha;
use App\Models\SiteSetting;
use App\Models\Score;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $topSanghas = Sangha::query()
                ->with('monastery')
                ->leftJoin('scores', 'scores.sangha_id', '=', 'sanghas.id')
                ->select('sanghas.*')
                ->selectRaw('COALESCE(SUM(scores.value), 0) as total_score')
                ->selectRaw('COALESCE(AVG(scores.value), 0) as average_score')
                ->selectRaw('COUNT(scores.id) as score_count')
                ->when($request->filled('subject_id'), fn ($query) => $query->where('scores.subject_id', $request->subject_id))
                ->when($request->filled('exam_id'), fn ($query) => $query->where('scores.exam_id', $request->exam_id))
                ->when($request->filled('sangha_id'), fn ($query) => $query->where('sanghas.id', $request->sangha_id))
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('sanghas.name', 'like', "%{$search}%")
                            ->orWhere('sanghas.username', 'like', "%{$search}%");
                    });
                })
                ->groupBy('sanghas.id')
                ->havingRaw('COUNT(scores.id) > 0')
                ->orderByDesc('total_score')
                ->orderByDesc('average_score')
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
        }

        $query = Score::with(['sangha', 'subject', 'exam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->whereHas('sangha', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$search}%"));
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
            $query->orderBy('scores.' . $sort, $order);
        } else {
            $query->latest();
        }
        $scores = $query->paginate(admin_per_page(15))->withQueryString();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sanghas = Sangha::with('monastery')->orderBy('name')->get();
        $exams = Exam::orderBy('exam_date', 'desc')->get();

        return view('admin.scores.index', compact('scores', 'subjects', 'sanghas', 'exams', 'screen', 'topSanghas'));
    }

    public function create(Request $request): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sanghas = Sangha::with('monastery')->orderBy('name')->get();
        $exams = Exam::orderBy('exam_date', 'desc')->get();
        return view('admin.scores.create', compact('subjects', 'sanghas', 'exams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sangha_id' => 'required|exists:sanghas,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'nullable|exists:exams,id',
            'value' => 'required|numeric|min:0',
            'moderation_decision' => 'nullable|in:pass,fail',
        ]);

        Score::create($validated);
        return redirect()->route('admin.scores.index')->with('success', 'Score created successfully.');
    }

    public function edit(Score $score): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sanghas = Sangha::with('monastery')->orderBy('name')->get();
        $exams = Exam::orderBy('exam_date', 'desc')->get();
        return view('admin.scores.edit', compact('score', 'subjects', 'sanghas', 'exams'));
    }

    public function update(Request $request, Score $score): RedirectResponse
    {
        $validated = $request->validate([
            'sangha_id' => 'required|exists:sanghas,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'nullable|exists:exams,id',
            'value' => 'required|numeric|min:0',
            'moderation_decision' => 'nullable|in:pass,fail',
        ]);

        $score->update($validated);

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
        $topSanghas = Sangha::query()
            ->leftJoin('scores', 'scores.sangha_id', '=', 'sanghas.id')
            ->select('sanghas.*')
            ->selectRaw('COALESCE(SUM(scores.value), 0) as total_score')
            ->selectRaw('COALESCE(AVG(scores.value), 0) as average_score')
            ->selectRaw('COUNT(scores.id) as score_count')
            ->groupBy('sanghas.id')
            ->havingRaw('COUNT(scores.id) > 0')
            ->orderByDesc('total_score')
            ->orderByDesc('average_score')
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

        $snapshot = [
            'generated_at' => now()->toDateTimeString(),
            'pass_sanghas' => $passSanghas->map(function ($sangha) {
                return [
                    'id' => $sangha->id,
                    'name' => $sangha->name,
                    'monastery_name' => $sangha->monastery->name ?? '—',
                ];
            })->values()->all(),
        ];

        SiteSetting::set('pass_sanghas_snapshot', json_encode($snapshot, JSON_UNESCAPED_UNICODE));

        return response()->json([
            'message' => 'Generated successful.',
            'generated_at' => $snapshot['generated_at'],
        ]);
    }
}
