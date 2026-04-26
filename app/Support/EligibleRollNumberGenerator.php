<?php

namespace App\Support;

use App\Models\Sangha;
use Illuminate\Support\Facades\DB;

/**
 * Next 6-digit zero-padded eligible roll per monastery, scoped by programme hub
 * (Primary, Intermediate, Level 1–3) — independent of assigned exam_id.
 */
class EligibleRollNumberGenerator
{
    /** @var list<string> */
    public const PROGRAMME_ENTITY_TYPES = [
        'programme_primary',
        'programme_intermediate',
        'programme_level_1',
        'programme_level_2',
        'programme_level_3',
    ];

    public static function isProgrammeEntityType(?string $entityType): bool
    {
        return is_string($entityType)
            && $entityType !== ''
            && in_array($entityType, self::PROGRAMME_ENTITY_TYPES, true);
    }

    /**
     * @param  ?string  $programmeEntityType  One of {@see self::PROGRAMME_ENTITY_TYPES}, or null for sanghas with no programme hub CFV yet
     * @param  ?int  $excludeSanghaId  Exclude this sangha from the max (e.g. programme promotion before roll overwrite)
     */
    public static function next(int $monasteryId, ?string $programmeEntityType = null, ?int $excludeSanghaId = null): string
    {
        if ($monasteryId < 1) {
            throw new \InvalidArgumentException('monastery_id is required to generate an eligible roll number.');
        }

        return DB::transaction(function () use ($monasteryId, $programmeEntityType, $excludeSanghaId): string {
            $query = Sangha::query()
                ->where('sanghas.monastery_id', $monasteryId)
                ->whereNotNull('sanghas.eligible_roll_number')
                ->where('sanghas.eligible_roll_number', '!=', '');

            if ($excludeSanghaId !== null && $excludeSanghaId > 0) {
                $query->where('sanghas.id', '!=', $excludeSanghaId);
            }

            if (self::isProgrammeEntityType($programmeEntityType)) {
                $query->whereExists(function ($sub) use ($programmeEntityType) {
                    $sub->select(DB::raw(1))
                        ->from('custom_field_values')
                        ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                        ->where('custom_field_values.entity_type', $programmeEntityType);
                });
            } else {
                $query->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('custom_field_values')
                        ->whereColumn('custom_field_values.entity_id', 'sanghas.id')
                        ->whereIn('custom_field_values.entity_type', self::PROGRAMME_ENTITY_TYPES);
                });
            }

            $existingSerials = $query
                ->lockForUpdate()
                ->pluck('sanghas.eligible_roll_number')
                ->map(function ($value): int {
                    $serial = trim((string) $value);

                    return ctype_digit($serial) ? (int) $serial : 0;
                });

            $max = $existingSerials->max();
            $nextSerial = ($max === null ? 0 : (int) $max) + 1;

            if ($nextSerial > 999999) {
                throw new \RuntimeException('Eligible roll number serial exceeded 6 digits.');
            }

            return str_pad((string) $nextSerial, 6, '0', STR_PAD_LEFT);
        });
    }
}
