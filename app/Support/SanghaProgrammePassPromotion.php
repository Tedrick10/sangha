<?php

namespace App\Support;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Sangha;
use App\Models\Score;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

/**
 * When the admin publishes the pass list, Level 1 / Level 2 programme sanghas advance to the next
 * programme tier (custom_field_values entity_type) and return to eligible for the next intake.
 */
class SanghaProgrammePassPromotion
{
    /** @var array<string, string> */
    private const NEXT_TIER = [
        'programme_level_1' => 'programme_level_2',
        'programme_level_2' => 'programme_level_3',
    ];

    public static function promoteAfterPassPublish(EloquentCollection $passSanghas): void
    {
        if ($passSanghas->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($passSanghas) {
            foreach ($passSanghas as $sangha) {
                self::promoteOneIfApplicable((int) $sangha->id);
            }
        });
    }

    private static function promoteOneIfApplicable(int $sanghaId): void
    {
        $from = self::resolveSourceProgrammeTier($sanghaId);
        if ($from === null) {
            return;
        }

        $to = self::NEXT_TIER[$from] ?? null;
        if ($to === null) {
            return;
        }

        self::copyProgrammeTierFields($sanghaId, $from, $to);
        self::setLevelInformationLabel($sanghaId, $to);

        CustomFieldValue::query()
            ->where('entity_id', $sanghaId)
            ->where('entity_type', $from)
            ->delete();

        Score::query()->where('sangha_id', $sanghaId)->delete();

        $sangha = Sangha::query()->whereKey($sanghaId)->first();
        if (! $sangha) {
            return;
        }

        $sangha->exam_id = null;
        $sangha->desk_number = null;
        $sangha->eligible_roll_number = EligibleRollNumberGenerator::next((int) $sangha->monastery_id, $to, (int) $sangha->id);
        $sangha->top20_position = null;
        $sangha->workflow_status = Sangha::STATUS_ELIGIBLE;
        $sangha->approved = false;
        $sangha->rejection_reason = null;
        $sangha->save();
    }

    /**
     * Prefer the furthest tier (L2 before L1) so we advance one step from the programme they actually completed.
     */
    private static function resolveSourceProgrammeTier(int $sanghaId): ?string
    {
        if (CustomFieldValue::query()->where('entity_id', $sanghaId)->where('entity_type', 'programme_level_2')->exists()) {
            return 'programme_level_2';
        }
        if (CustomFieldValue::query()->where('entity_id', $sanghaId)->where('entity_type', 'programme_level_1')->exists()) {
            return 'programme_level_1';
        }

        return null;
    }

    private static function copyProgrammeTierFields(int $sanghaId, string $fromEntity, string $toEntity): void
    {
        $sourceFields = CustomField::forEntity($fromEntity)->get();
        $targetBySlug = CustomField::forEntity($toEntity)->get()->keyBy('slug');

        $valuesByFieldId = CustomFieldValue::query()
            ->where('entity_id', $sanghaId)
            ->where('entity_type', $fromEntity)
            ->pluck('value', 'custom_field_id');

        foreach ($sourceFields as $sourceField) {
            $value = $valuesByFieldId->get($sourceField->id);
            if ($value === null || $value === '') {
                continue;
            }
            $targetField = $targetBySlug->get($sourceField->slug);
            if (! $targetField) {
                continue;
            }

            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $targetField->id,
                    'entity_type' => $toEntity,
                    'entity_id' => $sanghaId,
                ],
                ['value' => (string) $value]
            );
        }
    }

    private static function setLevelInformationLabel(int $sanghaId, string $toEntity): void
    {
        $field = CustomField::forEntity($toEntity)->where('slug', 'level_information')->first();
        if (! $field) {
            return;
        }

        $label = $toEntity === 'programme_level_2' ? 'Level 2' : 'Level 3';

        CustomFieldValue::updateOrCreate(
            [
                'custom_field_id' => $field->id,
                'entity_type' => $toEntity,
                'entity_id' => $sanghaId,
            ],
            ['value' => $label]
        );
    }
}
