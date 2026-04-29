<?php

namespace App\Support;

use App\Models\CustomField;
use App\Models\Sangha;
use App\Models\Score;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Merges published pass-list snapshot rows with current DB values for roll, desk, and level.
 */
class PassSanghaListDisplay
{
    /** @var list<string> */
    private const PROGRAMME_ENTITY_TYPES = [
        'programme_primary',
        'programme_intermediate',
        'programme_level_1',
        'programme_level_2',
        'programme_level_3',
    ];

    /**
     * Values from custom field `level_information` on programme hub entities (Primary … Level 3).
     *
     * @param  list<int>  $sanghaIds
     * @return array<int, string>
     */
    public static function programmeLevelInformationBySanghaIds(array $sanghaIds): array
    {
        return self::programmeLevelLabelsBySanghaIds($sanghaIds);
    }

    /**
     * @param  Collection<int, array<string, mixed>|object>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    public static function enrichSnapshotRows(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $ids = $rows->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();
        if ($ids === []) {
            return $rows->map(fn ($row) => is_array($row) ? $row : (array) $row);
        }

        $sanghas = Sangha::query()
            ->whereIn('id', $ids)
            ->with(['exam.examType', 'monastery'])
            ->get()
            ->keyBy('id');

        $metaBySanghaId = Score::latestScoreRowMetaBySanghaIds($ids);
        $latestScoreIdRows = Score::query()
            ->selectRaw('MAX(id) as id')
            ->whereIn('sangha_id', $ids)
            ->groupBy('sangha_id')
            ->pluck('id');
        $latestScoresBySanghaId = $latestScoreIdRows->isEmpty()
            ? collect()
            : Score::query()
                ->whereIn('id', $latestScoreIdRows)
                ->with(['exam.examType'])
                ->get()
                ->keyBy('sangha_id');

        $levelBySanghaId = static::programmeLevelLabelsBySanghaIds($ids);

        return $rows->map(function ($row) use ($sanghas, $metaBySanghaId, $latestScoresBySanghaId, $levelBySanghaId) {
            $rowArr = is_array($row) ? $row : (array) $row;
            $id = (int) ($rowArr['id'] ?? 0);
            $sangha = $sanghas->get($id);
            if (! $sangha) {
                $legacyDeskOnly = $rowArr['desk_number'] ?? null;
                if ($legacyDeskOnly !== null && ctype_digit((string) $legacyDeskOnly)) {
                    $rowArr['desk_display'] = MonasteryPortalResultsSnapshot::formatDeskDisplaySix('', $legacyDeskOnly);
                }

                return $rowArr;
            }

            $meta = $metaBySanghaId->get($id, [
                'father_name' => null,
                'nrc_number' => null,
                'candidate_ref' => null,
                'desk_number' => null,
            ]);

            $deskRaw = $meta['desk_number'];
            if (($deskRaw === null || $deskRaw === '') && $sangha->desk_number !== null) {
                $deskRaw = (string) $sangha->desk_number;
            }

            $latestScore = $latestScoresBySanghaId->get($id);
            $deskPrefix = $latestScore?->exam?->desk_number_prefix ?? $sangha->exam?->desk_number_prefix ?? '';

            $deskDisplay = ($deskRaw !== null && (string) $deskRaw !== '')
                ? MonasteryPortalResultsSnapshot::formatDeskDisplaySix($deskPrefix, $deskRaw)
                : null;

            $legacyDesk = $rowArr['desk_display'] ?? $rowArr['desk_number'] ?? null;
            if ($deskDisplay === null && $legacyDesk !== null && (string) $legacyDesk !== '') {
                $legacy = (string) $legacyDesk;
                if (ctype_digit($legacy)) {
                    $deskDisplay = MonasteryPortalResultsSnapshot::formatDeskDisplaySix('', $legacy);
                } else {
                    $deskDisplay = $legacy;
                }
            }

            $examTypeLabel = $latestScore?->exam?->examType?->name
                ?? $sangha->exam?->examType?->name;
            $liveProgramme = $levelBySanghaId[$id] ?? null;

            $rollDisplayLive = MonasteryPortalResultsSnapshot::formatRollDisplaySix($sangha->eligible_roll_number);

            $examYearLive = $sangha->exam?->exam_date?->format('Y');

            $snapRoll = self::nonEmptyString($rowArr['eligible_roll_number'] ?? null);
            $snapRollDisplay = self::nonEmptyString($rowArr['roll_display'] ?? null);
            $snapDeskDisplay = self::nonEmptyString($rowArr['desk_display'] ?? null);
            $snapDeskNumber = self::nonEmptyString($rowArr['desk_number'] ?? null);
            $snapProgramme = self::nonEmptyString($rowArr['programme_level'] ?? null);
            $snapLevel = self::nonEmptyString($rowArr['level_name'] ?? null);
            $snapExamYear = self::nonEmptyString($rowArr['exam_year'] ?? null);
            $snapExamName = self::nonEmptyString($rowArr['exam_name'] ?? null);
            $snapExamId = $rowArr['exam_id'] ?? null;

            $finalRoll = $snapRoll ?? $sangha->eligible_roll_number;
            $finalRollDisplay = $snapRollDisplay
                ?? ($snapRoll !== null ? MonasteryPortalResultsSnapshot::formatRollDisplaySix($snapRoll) : null)
                ?? $rollDisplayLive;

            $finalDeskDisplay = $snapDeskDisplay ?? $deskDisplay ?? $snapDeskNumber ?? $legacyDesk;

            $finalLevel = $snapProgramme ?? $snapLevel ?? $liveProgramme ?? $examTypeLabel;

            $finalExamYear = $snapExamYear ?? $examYearLive ?? ($rowArr['exam_year'] ?? null);
            $finalExamName = $snapExamName ?? ($sangha->exam?->name ?? ($rowArr['exam_name'] ?? null));
            $finalExamId = ($snapExamId !== null && (int) $snapExamId > 0)
                ? (int) $snapExamId
                : ($sangha->exam_id ?? ($rowArr['exam_id'] ?? null));

            $fatherSnap = self::nonEmptyString($rowArr['father_name'] ?? null)
                ?? self::nonEmptyString($rowArr['latest_score_father_name'] ?? null);
            $nrcSnap = self::nonEmptyString($rowArr['nrc_number'] ?? null)
                ?? self::nonEmptyString($rowArr['latest_score_nrc_number'] ?? null);

            return array_merge($rowArr, [
                'exam_type_id' => $rowArr['exam_type_id'] ?? $latestScore?->exam?->exam_type_id ?? $sangha->exam?->exam_type_id,
                'name' => $sangha->name,
                'monastery_name' => $sangha->monastery->name ?? ($rowArr['monastery_name'] ?? '—'),
                'exam_id' => $finalExamId,
                'exam_year' => $finalExamYear,
                'exam_name' => $finalExamName,
                'eligible_roll_number' => $finalRoll,
                'roll_display' => $finalRollDisplay,
                'desk_display' => $finalDeskDisplay,
                'desk_number' => $finalDeskDisplay,
                'programme_level' => $snapProgramme ?? $liveProgramme,
                'level_name' => $finalLevel,
                'father_name' => self::nonEmptyString($meta['father_name'] ?? null) ?? $fatherSnap,
                'nrc_number' => self::nonEmptyString($meta['nrc_number'] ?? null) ?? $nrcSnap,
            ]);
        });
    }

    /**
     * Remove duplicate rows by exam + roll number while preserving order.
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    public static function uniqueByExamRoll(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $seen = [];

        return $rows->filter(function (array $row) use (&$seen): bool {
            $roll = self::nonEmptyString($row['eligible_roll_number'] ?? null)
                ?? self::nonEmptyString($row['roll_display'] ?? null);
            if ($roll === null) {
                return true;
            }

            $examId = (int) ($row['exam_id'] ?? 0);
            $examTypeId = (int) ($row['exam_type_id'] ?? 0);
            $examYear = self::nonEmptyString($row['exam_year'] ?? null) ?? '';
            $scope = $examId > 0
                ? 'exam:'.$examId
                : ('type:'.$examTypeId.'|year:'.$examYear);
            $key = $scope.'|roll:'.mb_strtolower($roll);

            if (isset($seen[$key])) {
                return false;
            }

            $seen[$key] = true;

            return true;
        })->values();
    }

    /**
     * @param  list<int>  $sanghaIds
     * @return array<int, string>
     */
    private static function nonEmptyString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    private static function programmeLevelLabelsBySanghaIds(array $sanghaIds): array
    {
        $fieldIds = CustomField::query()
            ->where('slug', 'level_information')
            ->whereIn('entity_type', self::PROGRAMME_ENTITY_TYPES)
            ->pluck('id');

        if ($fieldIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('custom_field_values as cfv')
            ->whereIn('cfv.custom_field_id', $fieldIds->all())
            ->whereIn('cfv.entity_id', $sanghaIds)
            ->whereIn('cfv.entity_type', self::PROGRAMME_ENTITY_TYPES)
            ->orderByDesc('cfv.id')
            ->get(['cfv.entity_id', 'cfv.value']);

        $out = [];
        foreach ($rows as $r) {
            $eid = (int) $r->entity_id;
            if (isset($out[$eid])) {
                continue;
            }
            $val = $r->value;
            if ($val !== null && (string) $val !== '') {
                $out[$eid] = (string) $val;
            }
        }

        return $out;
    }
}
