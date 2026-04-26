<?php

namespace App\Models;

use App\Support\MonasteryPortalResultsSnapshot;
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

    /**
     * Load the Sangha row for an approved_sangha field value (must belong to this request’s monastery).
     */
    public function approvedSanghaForFieldValue(?string $raw): ?Sangha
    {
        if (! filled($raw) || ! ctype_digit((string) $raw)) {
            return null;
        }

        return Sangha::query()
            ->whereKey((int) $raw)
            ->where('monastery_id', $this->monastery_id)
            ->with(['monastery:id,name', 'exam:id,name,exam_date'])
            ->first();
    }

    /**
     * Load the Exam row for monastery_exam exam_session (stored value is exam id).
     */
    public function examForSessionFieldValue(?string $raw): ?Exam
    {
        if (! filled($raw) || ! ctype_digit((string) $raw)) {
            return null;
        }

        return Exam::query()
            ->whereKey((int) $raw)
            ->with(['examType:id,name', 'subjects'])
            ->first();
    }

    /**
     * Human-readable value for portal / admin detail views (e.g. approved sangha id → name).
     */
    public function displaySubmittedValue(CustomField $field, ?string $raw): string
    {
        if (! filled($raw)) {
            return '';
        }

        if ($field->type === 'approved_sangha' && ctype_digit((string) $raw)) {
            $row = Sangha::query()
                ->where('id', (int) $raw)
                ->where('monastery_id', $this->monastery_id)
                ->first(['name', 'username']);

            if ($row) {
                $label = (string) $row->name;
                if (filled($row->username)) {
                    $label .= ' ('.$row->username.')';
                }

                return $label;
            }

            return '#'.$raw;
        }

        if ($field->type === 'monastery_select' && ctype_digit((string) $raw)) {
            $name = Monastery::query()->whereKey((int) $raw)->value('name');

            return $name ? (string) $name : '#'.$raw;
        }

        if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session' && ctype_digit((string) $raw)) {
            $name = Exam::query()->whereKey((int) $raw)->value('name');

            return $name ? (string) $name : '#'.$raw;
        }

        return (string) $raw;
    }

    private function existingValueForField(CustomField $field): ?string
    {
        return $this->getRequestFieldValue($field);
    }

    public function summaryPreview(): string
    {
        $entityType = $this->portalCustomFieldEntityType();
        $fields = CustomField::forEntity($entityType)->orderBy('sort_order')->orderBy('name')->get();

        if ($entityType === 'request') {
            $previewOrder = [
                'transfer_from',
                'transfer_to',
                'transfer_sangha_id',
                'transfer_date',
                'transfer_details',
                'transfer_attachment',
            ];
            $fields = $fields->sortBy(function ($f) use ($previewOrder) {
                $pos = array_search($f->slug, $previewOrder, true);

                return $pos !== false ? $pos : 1000;
            })->values();
        }

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
            if ($field->type === 'approved_sangha') {
                $label = $this->displaySubmittedValue($field, $v);

                return Str::limit($field->name.': '.$label, 120);
            }
            if ($field->type === 'monastery_select') {
                $label = $this->displaySubmittedValue($field, $v);

                return Str::limit($field->name.': '.$label, 120);
            }
            if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session') {
                $label = $this->displaySubmittedValue($field, $v);

                return Str::limit($field->name.': '.$label, 120);
            }

            return Str::limit((string) $v, 80);
        }

        return '—';
    }

    /**
     * For general (non-exam) transfer requests: validate destination + sangha before admin approves.
     *
     * @return string|null Error message for the admin, or null if OK / not applicable.
     */
    public function validateTransferDataForApproval(): ?string
    {
        if ($this->isExamFormSubmission()) {
            return null;
        }

        $resolved = $this->resolveTransferSanghaAndDestinationIds();
        if ($resolved === null) {
            return t('transfer_approve_missing_fields', 'This transfer is missing a destination monastery or sangha. Update the submission before approving.');
        }

        $originId = (int) $this->monastery_id;
        $sanghaId = $resolved['sangha_id'];
        $destId = $resolved['dest_id'];

        if ($destId === $originId) {
            return t('transfer_approve_destination_same_as_origin', 'Destination monastery must differ from the submitting monastery.');
        }

        $sangha = Sangha::query()->whereKey($sanghaId)->first(['id', 'monastery_id']);
        if (! $sangha || (int) $sangha->monastery_id !== $originId) {
            return t('transfer_approve_sangha_not_at_origin', 'The selected sangha is no longer registered under this monastery; transfer cannot be approved.');
        }

        $dest = Monastery::query()
            ->whereKey($destId)
            ->where('approved', true)
            ->where('is_active', true)
            ->first(['id']);
        if (! $dest) {
            return t('transfer_approve_destination_invalid', 'The destination monastery is not active or not approved.');
        }

        return null;
    }

    /**
     * After status is set to approved: move sangha to destination and migrate portal pass/fail snapshot rows.
     *
     * @throws \RuntimeException When relocation cannot be completed (caller should roll back the transaction).
     */
    public function applyApprovedTransferRelocation(): void
    {
        if ($this->isExamFormSubmission()) {
            return;
        }

        $resolved = $this->resolveTransferSanghaAndDestinationIds();
        if ($resolved === null) {
            throw new \RuntimeException((string) t('transfer_approve_missing_fields', 'This transfer is missing a destination monastery or sangha.'));
        }

        $originId = (int) $this->monastery_id;
        $sanghaId = $resolved['sangha_id'];
        $destId = $resolved['dest_id'];

        if ($destId === $originId) {
            throw new \RuntimeException((string) t('transfer_approve_destination_same_as_origin', 'Destination monastery must differ from the submitting monastery.'));
        }

        $updated = Sangha::query()
            ->whereKey($sanghaId)
            ->where('monastery_id', $originId)
            ->update([
                'monastery_id' => $destId,
                'desk_number' => null,
            ]);

        if ($updated !== 1) {
            throw new \RuntimeException((string) t('transfer_approve_sangha_not_at_origin', 'The selected sangha is no longer registered under this monastery; transfer cannot be approved.'));
        }

        MonasteryPortalResultsSnapshot::moveSanghaRowsBetweenMonasteries($sanghaId, $originId, $destId);
    }

    /**
     * @return array{sangha_id: int, dest_id: int}|null
     */
    private function resolveTransferSanghaAndDestinationIds(): ?array
    {
        $sanghaField = CustomField::query()->where('entity_type', 'request')->where('slug', 'transfer_sangha_id')->first();
        $destField = CustomField::query()->where('entity_type', 'request')->where('slug', 'transfer_to')->first();
        if (! $sanghaField || ! $destField) {
            return null;
        }

        $sRaw = $this->getRequestFieldValue($sanghaField);
        $dRaw = $this->getRequestFieldValue($destField);
        if (! filled($sRaw) || ! ctype_digit((string) $sRaw) || ! filled($dRaw) || ! ctype_digit((string) $dRaw)) {
            return null;
        }

        return [
            'sangha_id' => (int) $sRaw,
            'dest_id' => (int) $dRaw,
        ];
    }
}
