<?php

namespace App\Support;

use App\Models\Sangha;
use App\Models\Score;
use Illuminate\Support\Facades\DB;

class MonasteryPortalResultsSnapshot
{
    public const KEY = 'monastery_portal_results_snapshot';

    public static function key(): string
    {
        return self::KEY;
    }

    /**
     * Build pass/fail lists per monastery (same rules as monastery portal had when live).
     *
     * @return array{generated_at: string, monasteries: array<string, array{pass: list<array>, fail: list<array>}>}
     */
    public static function build(?string $generatedAt = null): array
    {
        $generatedAt ??= now()->toDateTimeString();

        $passAll = static::basePassQuery()->orderBy('name')->get();
        $failAll = static::baseFailQuery()->orderBy('name')->get();

        $allIds = $passAll->pluck('id')->merge($failAll->pluck('id'))->unique()->values()->all();
        $scoreMeta = Score::latestScoreRowMetaBySanghaIds($allIds);

        $serialize = function (Sangha $sangha) use ($scoreMeta) {
            $row = $scoreMeta->get($sangha->id, ['father_name' => null, 'nrc_number' => null, 'candidate_ref' => null]);

            return [
                'id' => $sangha->id,
                'name' => $sangha->name,
                'exam_name' => $sangha->exam?->name,
                'latest_score_father_name' => $row['father_name'],
                'latest_score_nrc_number' => $row['nrc_number'],
                'candidate_ref' => $row['candidate_ref'],
            ];
        };

        $monasteries = [];
        foreach ($passAll as $sangha) {
            $k = (string) $sangha->monastery_id;
            $monasteries[$k] ??= ['pass' => [], 'fail' => []];
            $monasteries[$k]['pass'][] = $serialize($sangha);
        }
        foreach ($failAll as $sangha) {
            $k = (string) $sangha->monastery_id;
            $monasteries[$k] ??= ['pass' => [], 'fail' => []];
            $monasteries[$k]['fail'][] = $serialize($sangha);
        }

        return [
            'generated_at' => $generatedAt,
            'monasteries' => $monasteries,
        ];
    }

    private static function basePassQuery()
    {
        return Sangha::query()
            ->with('exam')
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
            });
    }

    private static function baseFailQuery()
    {
        return Sangha::query()
            ->with('exam')
            ->where(function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->join('subjects', 'subjects.id', '=', 'scores.subject_id')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->whereNotNull('subjects.moderation_mark')
                        ->whereRaw('scores.value < subjects.moderation_mark');
                })->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->where('scores.moderation_decision', 'fail');
                });
            });
    }
}
