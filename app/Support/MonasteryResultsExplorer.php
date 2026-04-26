<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Groups published monastery pass/fail snapshot rows by exam year, level, and exam for portal navigation.
 */
class MonasteryResultsExplorer
{
    public const YEAR_UNKNOWN = '__unknown__';

    public const LEVEL_NONE = '__none__';

    public static function yearKeyFromRow(object|array $row): string
    {
        $a = is_array($row) ? $row : (array) $row;
        $y = $a['exam_year'] ?? null;
        if ($y === null || $y === '') {
            return self::YEAR_UNKNOWN;
        }

        return (string) $y;
    }

    public static function levelKeyFromRow(object|array $row): string
    {
        $a = is_array($row) ? $row : (array) $row;
        $l = $a['level_name'] ?? null;
        if ($l === null || trim((string) $l) === '') {
            return self::LEVEL_NONE;
        }

        return trim((string) $l);
    }

    public static function yearLabel(string $yearKey): string
    {
        return $yearKey === self::YEAR_UNKNOWN
            ? t('year_not_set', 'Year not set')
            : $yearKey;
    }

    public static function levelLabel(string $levelKey): string
    {
        return $levelKey === self::LEVEL_NONE ? '—' : $levelKey;
    }

    /**
     * @param  Collection<int, object>  $pass
     * @param  Collection<int, object>  $fail
     * @return Collection<int, array{year_key: string, year_label: string, pass_count: int, fail_count: int, total: int}>
     */
    public static function yearCards(Collection $pass, Collection $fail): Collection
    {
        if ($pass->isEmpty() && $fail->isEmpty()) {
            return collect();
        }

        $keys = $pass->merge($fail)
            ->map(fn ($r) => self::yearKeyFromRow($r))
            ->unique()
            ->values();

        return $keys->map(function (string $yearKey) use ($pass, $fail) {
            $p = $pass->filter(fn ($r) => self::yearKeyFromRow($r) === $yearKey);
            $f = $fail->filter(fn ($r) => self::yearKeyFromRow($r) === $yearKey);

            return [
                'year_key' => $yearKey,
                'year_label' => self::yearLabel($yearKey),
                'pass_count' => $p->count(),
                'fail_count' => $f->count(),
                'total' => $p->count() + $f->count(),
            ];
        })->sort(function (array $a, array $b) {
            return self::compareYearKeys($a['year_key'], $b['year_key']);
        })->values();
    }

    /**
     * @param  Collection<int, object>  $pass
     * @param  Collection<int, object>  $fail
     * @return Collection<int, array{level_key: string, level_label: string, pass_count: int, fail_count: int}>
     */
    public static function levelCards(Collection $pass, Collection $fail, string $yearKey): Collection
    {
        $passY = $pass->filter(fn ($r) => self::yearKeyFromRow($r) === $yearKey);
        $failY = $fail->filter(fn ($r) => self::yearKeyFromRow($r) === $yearKey);
        if ($passY->isEmpty() && $failY->isEmpty()) {
            return collect();
        }

        $keys = $passY->merge($failY)
            ->map(fn ($r) => self::levelKeyFromRow($r))
            ->unique()
            ->values();

        return $keys->map(function (string $levelKey) use ($passY, $failY) {
            $p = $passY->filter(fn ($r) => self::levelKeyFromRow($r) === $levelKey);
            $f = $failY->filter(fn ($r) => self::levelKeyFromRow($r) === $levelKey);

            return [
                'level_key' => $levelKey,
                'level_label' => self::levelLabel($levelKey),
                'pass_count' => $p->count(),
                'fail_count' => $f->count(),
            ];
        })->sortBy('level_label')->values();
    }

    /**
     * @param  Collection<int, object>  $pass
     * @param  Collection<int, object>  $fail
     * @return Collection<int, array{exam_id: int, exam_name: string, pass_count: int, fail_count: int}>
     */
    public static function examCards(Collection $pass, Collection $fail, string $yearKey, string $levelKey): Collection
    {
        $passF = $pass->filter(fn ($r) => self::yearKeyFromRow($r) === $yearKey && self::levelKeyFromRow($r) === $levelKey);
        $failF = $fail->filter(fn ($r) => self::yearKeyFromRow($r) === $yearKey && self::levelKeyFromRow($r) === $levelKey);
        if ($passF->isEmpty() && $failF->isEmpty()) {
            return collect();
        }

        $examIds = $passF->pluck('exam_id')
            ->merge($failF->pluck('exam_id'))
            ->map(fn ($id) => (int) ($id ?? 0))
            ->unique()
            ->sort()
            ->values();

        return $examIds->map(function (int $examId) use ($passF, $failF) {
            $p = $passF->filter(fn ($r) => (int) ($r->exam_id ?? 0) === $examId);
            $f = $failF->filter(fn ($r) => (int) ($r->exam_id ?? 0) === $examId);
            $sample = $p->first() ?? $f->first();
            $name = $sample->exam_name ?? ($examId === 0 ? '—' : ('#'.$examId));

            return [
                'exam_id' => $examId,
                'exam_name' => $name,
                'pass_count' => $p->count(),
                'fail_count' => $f->count(),
            ];
        })->sortBy('exam_name')->values();
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return Collection<int, object>
     */
    public static function filterRows(
        Collection $rows,
        ?string $yearKey,
        ?string $levelKey,
        ?int $examId
    ): Collection {
        return $rows->values()->filter(function ($row) use ($yearKey, $levelKey, $examId) {
            if ($examId !== null && $examId > 0) {
                if ((int) (($row->exam_id ?? 0)) !== $examId) {
                    return false;
                }
            }
            if ($yearKey !== null) {
                if (self::yearKeyFromRow($row) !== $yearKey) {
                    return false;
                }
            }
            if ($levelKey !== null) {
                if (self::levelKeyFromRow($row) !== $levelKey) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private static function compareYearKeys(string $a, string $b): int
    {
        $unk = self::YEAR_UNKNOWN;
        if ($a === $unk && $b !== $unk) {
            return 1;
        }
        if ($b === $unk && $a !== $unk) {
            return -1;
        }

        return strcmp($b, $a);
    }
}
