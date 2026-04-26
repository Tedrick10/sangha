<?php

namespace App\Support;

use App\Models\Sangha;
use App\Models\Score;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class MonasteryPortalResultsSnapshot
{
    public const KEY = 'monastery_portal_results_snapshot';

    public static function key(): string
    {
        return self::KEY;
    }

    /** Six-digit roll display (matches admin scores / mandatory grid). */
    public static function formatRollDisplaySix(?string $eligibleRollNumber): ?string
    {
        if ($eligibleRollNumber === null || trim((string) $eligibleRollNumber) === '') {
            return null;
        }

        return str_pad(trim((string) $eligibleRollNumber), 6, '0', STR_PAD_LEFT);
    }

    /** Desk prefix + six-digit numeric suffix (matches monastery desk display). */
    public static function formatDeskDisplaySix(?string $prefix, $deskNumber): ?string
    {
        if ($deskNumber === null || (string) $deskNumber === '') {
            return null;
        }

        $p = (string) ($prefix ?? '');
        $pad = str_pad((string) (int) $deskNumber, 6, '0', STR_PAD_LEFT);

        return $p !== '' ? $p.$pad : $pad;
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
        $programmeBySanghaId = PassSanghaListDisplay::programmeLevelInformationBySanghaIds($allIds);

        $serialize = function (Sangha $sangha) use ($scoreMeta, $programmeBySanghaId) {
            $row = $scoreMeta->get($sangha->id, [
                'father_name' => null,
                'nrc_number' => null,
                'candidate_ref' => null,
                'desk_number' => null,
            ]);

            $deskPrefix = $sangha->exam?->desk_number_prefix ?? '';
            $deskRaw = $row['desk_number'] ?? null;
            if (($deskRaw === null || $deskRaw === '') && $sangha->desk_number !== null) {
                $deskRaw = (string) $sangha->desk_number;
            }
            $levelName = $sangha->exam?->examType?->name;
            $examYear = $sangha->exam?->exam_date?->format('Y');

            return [
                'id' => $sangha->id,
                'name' => $sangha->name,
                'exam_id' => $sangha->exam_id,
                'exam_year' => $examYear,
                'exam_name' => $sangha->exam?->name,
                'programme_level' => $programmeBySanghaId[$sangha->id] ?? null,
                'level_name' => $levelName,
                'latest_score_father_name' => $row['father_name'],
                'latest_score_nrc_number' => $row['nrc_number'],
                'candidate_ref' => $row['candidate_ref'],
                'eligible_roll_number' => $sangha->eligible_roll_number,
                'roll_display' => static::formatRollDisplaySix($sangha->eligible_roll_number),
                'desk_display' => static::formatDeskDisplaySix($deskPrefix, $deskRaw),
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

    /**
     * Merge a freshly built snapshot with the last stored one so pass/fail history survives later
     * "Generate" runs when promoted sanghas no longer appear in live pass queries.
     *
     * Rows are keyed by sangha id + exam id; the fresh build wins on conflict (same intake republished).
     *
     * @param  array{generated_at: string, monasteries: array<string, array{pass: list<array>, fail: list<array>}>}  $fresh
     * @param  array<string, mixed>|null  $previous
     * @return array{generated_at: string, monasteries: array<string, array{pass: list<array>, fail: list<array>}>}
     */
    public static function mergePreserveHistory(array $fresh, ?array $previous): array
    {
        $generatedAt = isset($fresh['generated_at']) && is_string($fresh['generated_at'])
            ? $fresh['generated_at']
            : now()->toDateTimeString();

        $prevMonasteries = is_array($previous['monasteries'] ?? null) ? $previous['monasteries'] : [];
        $freshMonasteries = is_array($fresh['monasteries'] ?? null) ? $fresh['monasteries'] : [];

        $ids = collect(array_keys($prevMonasteries))
            ->merge(array_keys($freshMonasteries))
            ->unique()
            ->values()
            ->all();

        $out = [];
        foreach ($ids as $monasteryId) {
            $out[(string) $monasteryId] = [
                'pass' => static::sortHistoryRows(static::mergePassFailRowLists(
                    $prevMonasteries[$monasteryId]['pass'] ?? [],
                    $freshMonasteries[$monasteryId]['pass'] ?? []
                )),
                'fail' => static::sortHistoryRows(static::mergePassFailRowLists(
                    $prevMonasteries[$monasteryId]['fail'] ?? [],
                    $freshMonasteries[$monasteryId]['fail'] ?? []
                )),
            ];
        }

        return [
            'generated_at' => $generatedAt,
            'monasteries' => $out,
        ];
    }

    /**
     * Move published portal pass/fail snapshot rows for one sangha from the origin monastery bucket
     * to the destination monastery (used when an admin-approved transfer changes sanghas.monastery_id).
     */
    public static function moveSanghaRowsBetweenMonasteries(int $sanghaId, int $fromMonasteryId, int $toMonasteryId): void
    {
        if ($sanghaId <= 0 || $fromMonasteryId <= 0 || $toMonasteryId <= 0 || $fromMonasteryId === $toMonasteryId) {
            return;
        }

        $raw = SiteSetting::get(self::KEY);
        if ($raw === null || $raw === '') {
            return;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded) || ! isset($decoded['monasteries']) || ! is_array($decoded['monasteries'])) {
            return;
        }

        $fk = (string) $fromMonasteryId;
        $tk = (string) $toMonasteryId;
        $monasteries = $decoded['monasteries'];
        if (! isset($monasteries[$fk]) || ! is_array($monasteries[$fk])) {
            return;
        }

        foreach (['pass', 'fail'] as $bucket) {
            $fromList = is_array($monasteries[$fk][$bucket] ?? null) ? $monasteries[$fk][$bucket] : [];
            [$moved, $rest] = static::partitionRowsBySanghaId($fromList, $sanghaId);
            $monasteries[$fk][$bucket] = static::sortHistoryRows($rest);
            if ($moved === []) {
                continue;
            }
            $monasteries[$tk] ??= ['pass' => [], 'fail' => []];
            $toExisting = is_array($monasteries[$tk][$bucket] ?? null) ? $monasteries[$tk][$bucket] : [];
            $monasteries[$tk][$bucket] = static::sortHistoryRows(static::mergePassFailRowLists($toExisting, $moved));
        }

        $decoded['monasteries'] = $monasteries;
        SiteSetting::set(self::KEY, json_encode($decoded, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param  list<mixed>  $rows
     * @return array{0: list<array<string, mixed>>, 1: list<array<string, mixed>>}
     */
    private static function partitionRowsBySanghaId(array $rows, int $sanghaId): array
    {
        $moved = [];
        $rest = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            if ((int) ($row['id'] ?? 0) === $sanghaId) {
                $moved[] = $row;
            } else {
                $rest[] = $row;
            }
        }

        return [$moved, $rest];
    }

    /**
     * @param  list<mixed>  $previousRows
     * @param  list<mixed>  $freshRows
     * @return list<array<string, mixed>>
     */
    private static function mergePassFailRowLists(array $previousRows, array $freshRows): array
    {
        /** @var array<string, array<string, mixed>> $byKey */
        $byKey = [];

        foreach ($previousRows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $byKey[static::historyRowKey($row)] = $row;
        }

        foreach ($freshRows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $byKey[static::historyRowKey($row)] = $row;
        }

        return array_values($byKey);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function historyRowKey(array $row): string
    {
        $id = (int) ($row['id'] ?? 0);
        $examId = isset($row['exam_id']) && $row['exam_id'] !== null && (int) $row['exam_id'] > 0
            ? (int) $row['exam_id']
            : 0;

        return $id.'_'.$examId;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private static function sortHistoryRows(array $rows): array
    {
        usort($rows, function (array $a, array $b): int {
            $yA = (string) ($a['exam_year'] ?? '');
            $yB = (string) ($b['exam_year'] ?? '');
            if ($yA !== $yB) {
                return strcmp($yB, $yA);
            }
            $lv = strcmp((string) ($a['level_name'] ?? ''), (string) ($b['level_name'] ?? ''));
            if ($lv !== 0) {
                return $lv;
            }

            return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        return $rows;
    }

    private static function basePassQuery()
    {
        return Sangha::query()
            ->with(['exam.examType'])
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
            ->with(['exam.examType'])
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
