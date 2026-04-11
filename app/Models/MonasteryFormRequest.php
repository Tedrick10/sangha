<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MonasteryFormRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'monastery_id',
        'exam_type_id',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function monastery(): BelongsTo
    {
        return $this->belongsTo(Monastery::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function isExamFormSubmission(): bool
    {
        return $this->exam_type_id !== null;
    }

    /**
     * Custom field definitions (and stored values in custom_field_values.entity_type) for this submission.
     */
    public function portalCustomFieldEntityType(): string
    {
        return $this->isExamFormSubmission() ? 'monastery_exam' : 'request';
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Persist portal custom fields for this submission (general: entity_type request; exam upload: monastery_exam).
     */
    public function syncRequestFieldValues(Request $httpRequest): void
    {
        $entityType = $this->portalCustomFieldEntityType();
        $customFields = CustomField::forEntity($entityType)->get();
        $values = $httpRequest->input('custom_fields', []);

        foreach ($customFields as $field) {
            $value = null;

            if (in_array($field->type, ['media', 'document', 'video'], true)) {
                $file = $httpRequest->file('custom_fields.'.$field->slug);
                if ($file?->isValid()) {
                    $value = $file->store('monastery-form-requests/'.$this->id, 'public');
                } else {
                    $value = $values[$field->slug] ?? $this->existingValueForField($field);
                }
            } else {
                $value = $values[$field->slug] ?? null;
            }

            if ($value === null && ! array_key_exists($field->slug, $values)) {
                continue;
            }

            if ($field->type === 'checkbox') {
                if (! in_array((string) $value, ['1', 'true', 'on'], true)) {
                    CustomFieldValue::where('custom_field_id', $field->id)
                        ->where('entity_type', $entityType)
                        ->where('entity_id', $this->id)
                        ->delete();

                    continue;
                }
                $value = '1';
            }

            $val = $value;
            if (is_array($val) || is_bool($val)) {
                $val = json_encode($val);
            }
            $val = (string) $val;

            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $field->id,
                    'entity_type' => $entityType,
                    'entity_id' => $this->id,
                ],
                ['value' => $val]
            );
        }
    }

    public function getRequestFieldValue(CustomField $field): ?string
    {
        $entityType = $this->portalCustomFieldEntityType();

        $value = CustomFieldValue::where('custom_field_id', $field->id)
            ->where('entity_type', $entityType)
            ->where('entity_id', $this->id)
            ->value('value');

        if ($value !== null && $value !== '') {
            return $value;
        }

        // Legacy: exam uploads used "request" definitions before monastery_exam existed.
        if ($this->isExamFormSubmission() && $entityType === 'monastery_exam') {
            $legacyFieldId = CustomField::query()
                ->where('entity_type', 'request')
                ->where('slug', $field->slug)
                ->value('id');
            if ($legacyFieldId) {
                return CustomFieldValue::where('custom_field_id', $legacyFieldId)
                    ->where('entity_type', 'request')
                    ->where('entity_id', $this->id)
                    ->value('value');
            }
        }

        return null;
    }

    private function existingValueForField(CustomField $field): ?string
    {
        return $this->getRequestFieldValue($field);
    }

    public function summaryPreview(): string
    {
        $entityType = $this->portalCustomFieldEntityType();
        $fields = CustomField::forEntity($entityType)->orderBy('sort_order')->orderBy('name')->get();

        // Before "Monastery exam" fields existed, exam uploads stored values against request definitions.
        if ($fields->isEmpty() && $this->isExamFormSubmission()) {
            $fields = CustomField::forEntity('request')->orderBy('sort_order')->orderBy('name')->get();
            foreach ($fields as $field) {
                $v = CustomFieldValue::where('custom_field_id', $field->id)
                    ->where('entity_type', 'request')
                    ->where('entity_id', $this->id)
                    ->value('value');
                if (! filled($v)) {
                    continue;
                }
                if (in_array($field->type, ['media', 'document', 'video'], true)) {
                    return Str::limit($field->name.': file', 80);
                }

                return Str::limit((string) $v, 80);
            }

            return '—';
        }

        foreach ($fields as $field) {
            $v = $this->getRequestFieldValue($field);
            if (! filled($v)) {
                continue;
            }
            if (in_array($field->type, ['media', 'document', 'video'], true)) {
                return Str::limit($field->name.': file', 80);
            }

            return Str::limit((string) $v, 80);
        }

        return '—';
    }
}
