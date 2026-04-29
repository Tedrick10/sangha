<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Score;
use App\Models\SiteSetting;
use App\Support\PassSanghaListDisplay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CleanPassController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $snapshotRaw = SiteSetting::get('pass_sanghas_snapshot');
        $snapshot = $snapshotRaw ? json_decode($snapshotRaw, true) : null;

        $rows = PassSanghaListDisplay::enrichSnapshotRows(
            collect($snapshot['pass_sanghas'] ?? [])
        )->values();
        $rows = $this->filterRowsByLivePassStatus($rows);
        $rows = PassSanghaListDisplay::uniqueByExamRoll($rows);

        $examTypes = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get();

        $selectedType = null;
        $examTypeId = null;

        if ($examTypes->isNotEmpty()) {
            if (! $request->filled('exam_type_id')) {
                return redirect()->route('admin.clean-pass.index', [
                    'exam_type_id' => $examTypes->first()->id,
                ]);
            }

            $examTypeId = (int) $request->exam_type_id;
            $selectedType = $examTypes->firstWhere('id', $examTypeId);

            if (! $selectedType) {
                return redirect()->route('admin.clean-pass.index', [
                    'exam_type_id' => $examTypes->first()->id,
                ]);
            }
        }

        $passSanghas = $rows;
        if ($selectedType) {
            $name = $selectedType->name;
            $passSanghas = $rows->filter(function (array $row) use ($examTypeId, $name) {
                $rid = $row['exam_type_id'] ?? null;
                if ($rid !== null && (int) $rid === $examTypeId) {
                    return true;
                }

                return ($row['level_name'] ?? '') === $name;
            })->values();
        }

        return view('admin.clean-pass.index', [
            'passSanghas' => $passSanghas,
            'examTypes' => $examTypes,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $response = app(ScoreController::class)->generatePassList();
        $payload = json_decode($response->getContent(), true);
        $message = is_array($payload) && isset($payload['message'])
            ? (string) $payload['message']
            : 'Published public pass list and monastery portal results.';

        $query = [];
        if ($request->filled('exam_type_id')) {
            $query['exam_type_id'] = (int) $request->exam_type_id;
        } else {
            $first = ExamType::query()
                ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
                ->orderByCanonical()
                ->first();
            if ($first) {
                $query['exam_type_id'] = $first->id;
            }
        }

        return redirect()
            ->route('admin.clean-pass.index', $query)
            ->with('success', $message);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_type_id' => ['required', 'integer', Rule::exists('exam_types', 'id')],
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['required', 'integer', 'min:1'],
        ]);

        $examTypeId = (int) $validated['exam_type_id'];
        $orderedIds = collect($validated['ordered_ids'])->map(fn ($id) => (int) $id)->values()->all();

        $snapshotRaw = SiteSetting::get('pass_sanghas_snapshot');
        $snapshot = $snapshotRaw ? json_decode($snapshotRaw, true) : null;
        if (! is_array($snapshot)) {
            return redirect()->route('admin.clean-pass.index', ['exam_type_id' => $examTypeId]);
        }
        $rows = collect($snapshot['pass_sanghas'] ?? [])->values();
        $examType = ExamType::query()->find($examTypeId, ['id', 'name']);
        if (! $examType || $rows->isEmpty()) {
            return redirect()->route('admin.clean-pass.index', ['exam_type_id' => $examTypeId]);
        }

        $matchedRows = $rows->filter(fn ($row) => $this->rowMatchesExamType($row, $examType))->values();
        $matchedById = [];
        foreach ($matchedRows as $row) {
            $sid = (int) ($row['id'] ?? 0);
            if ($sid > 0) {
                $matchedById[$sid] = $row;
            }
        }

        $reorderedSubset = [];
        foreach ($orderedIds as $sid) {
            if (isset($matchedById[$sid])) {
                $reorderedSubset[] = $matchedById[$sid];
                unset($matchedById[$sid]);
            }
        }
        foreach ($matchedRows as $row) {
            $sid = (int) ($row['id'] ?? 0);
            if ($sid > 0 && isset($matchedById[$sid])) {
                $reorderedSubset[] = $row;
            }
        }

        $out = [];
        $inserted = false;
        foreach ($rows as $row) {
            if ($this->rowMatchesExamType($row, $examType)) {
                if (! $inserted) {
                    foreach ($reorderedSubset as $r) {
                        $out[] = $r;
                    }
                    $inserted = true;
                }
                continue;
            }
            $out[] = $row;
        }

        $snapshot['pass_sanghas'] = $out;
        SiteSetting::set('pass_sanghas_snapshot', json_encode($snapshot, JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.clean-pass.index', ['exam_type_id' => $examTypeId])
            ->with('success', t('clean_pass_reorder_saved', 'Clean Pass order saved.'));
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    private function filterRowsByLivePassStatus(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $pairs = $rows->map(fn (array $row) => [
            'sangha_id' => (int) ($row['id'] ?? 0),
            'exam_id' => (int) ($row['exam_id'] ?? 0),
            'exam_type_id' => (int) ($row['exam_type_id'] ?? 0),
        ])->filter(fn ($p) => $p['sangha_id'] > 0 && $p['exam_id'] > 0)->values();

        if ($pairs->isEmpty()) {
            return collect();
        }

        $thresholdsByExam = [];
        foreach (SiteSetting::query()->where('key', 'like', 'moderation_thresholds_exam_%')->get(['key', 'value']) as $setting) {
            if (! preg_match('/^moderation_thresholds_exam_(\d+)$/', (string) $setting->key, $m)) {
                continue;
            }
            $examId = (int) $m[1];
            $decoded = $setting->value ? json_decode($setting->value, true) : null;
            if (! is_array($decoded) || $decoded === []) {
                continue;
            }
            $normalized = [];
            foreach ($decoded as $subjectId => $threshold) {
                if ($threshold === null || $threshold === '' || ! is_numeric((string) $threshold)) {
                    continue;
                }
                $normalized[(int) $subjectId] = (float) $threshold;
            }
            if ($normalized !== []) {
                $thresholdsByExam[$examId] = $normalized;
            }
        }

        $thresholdsByExamType = [];
        if ($thresholdsByExam !== []) {
            $examTypeByExamId = Exam::query()
                ->whereIn('id', array_keys($thresholdsByExam))
                ->get(['id', 'exam_type_id'])
                ->keyBy('id');
            foreach ($thresholdsByExam as $examId => $map) {
                $examTypeId = (int) ($examTypeByExamId->get((int) $examId)?->exam_type_id ?? 0);
                if ($examTypeId < 1) {
                    continue;
                }
                $existingExamId = (int) ($thresholdsByExamType[$examTypeId]['exam_id'] ?? 0);
                if ((int) $examId >= $existingExamId) {
                    $thresholdsByExamType[$examTypeId] = [
                        'exam_id' => (int) $examId,
                        'thresholds' => $map,
                    ];
                }
            }
        }

        $sanghaIds = $pairs->pluck('sangha_id')->unique()->all();
        $examIds = $pairs->pluck('exam_id')->unique()->all();
        $scoresByPair = Score::query()
            ->whereIn('sangha_id', $sanghaIds)
            ->whereIn('exam_id', $examIds)
            ->with('subject:id,pass_mark')
            ->get(['sangha_id', 'exam_id', 'subject_id', 'value', 'moderation_decision'])
            ->groupBy(fn ($s) => $s->sangha_id.'|'.$s->exam_id);

        $allowedPairKeys = [];
        foreach ($pairs as $pair) {
            $sanghaId = $pair['sangha_id'];
            $examId = $pair['exam_id'];
            $examTypeId = $pair['exam_type_id'];
            $key = $sanghaId.'|'.$examId;

            $scores = $scoresByPair->get($key, collect());
            if ($scores->isEmpty()) {
                continue;
            }
            if ($scores->contains(fn ($score) => $score->moderation_decision === 'fail')) {
                continue;
            }

            $thresholdMap = $thresholdsByExam[$examId] ?? [];
            if ($thresholdMap === [] && $examTypeId > 0) {
                $thresholdMap = $thresholdsByExamType[$examTypeId]['thresholds'] ?? [];
            }
            if ($thresholdMap !== []) {
                $bySubject = $scores->keyBy('subject_id');
                $below = false;
                foreach ($thresholdMap as $subjectId => $threshold) {
                    $value = $bySubject->get((int) $subjectId)?->value;
                    if ($value === null || (float) $value < (float) $threshold) {
                        $below = true;
                        break;
                    }
                }
                if ($below) {
                    continue;
                }
            }

            $hasPassMarkPass = $scores->contains(function ($score) {
                $passMark = $score->subject?->pass_mark;

                return $passMark !== null && $score->value !== null && (float) $score->value >= (float) $passMark;
            });
            $hasModerationPass = $scores->contains(fn ($score) => $score->moderation_decision === 'pass');
            if ($hasPassMarkPass || $hasModerationPass) {
                $allowedPairKeys[$key] = true;
            }
        }

        return $rows->filter(function (array $row) use ($allowedPairKeys) {
            $key = ((int) ($row['id'] ?? 0)).'|'.((int) ($row['exam_id'] ?? 0));

            return isset($allowedPairKeys[$key]);
        })->values();
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function rowMatchesExamType(array $row, ExamType $examType): bool
    {
        $rid = $row['exam_type_id'] ?? null;
        if ($rid !== null && (int) $rid === (int) $examType->id) {
            return true;
        }

        return (string) ($row['level_name'] ?? '') === (string) $examType->name;
    }
}
