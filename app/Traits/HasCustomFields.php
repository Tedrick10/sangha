<?php

namespace App\Traits;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HasCustomFields
{
    public function customFieldValues()
    {
        return $this->hasMany(CustomFieldValue::class, 'entity_id')
            ->where('entity_type', $this->getCustomFieldEntityType());
    }

    abstract protected function getCustomFieldEntityType(): string;

    public function getCustomFields()
    {
        return CustomField::where('entity_type', $this->getCustomFieldEntityType())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getCustomFieldValuesArray(): array
    {
        $fields = CustomField::forEntity($this->getCustomFieldEntityType())->get();
        $result = [];
        foreach ($fields as $field) {
            $result[$field->slug] = $this->getCustomFieldValue($field->slug);
        }
        return $result;
    }

    public function getCustomFieldValue(string $slug): ?string
    {
        $field = CustomField::where('entity_type', $this->getCustomFieldEntityType())
            ->where('slug', $slug)
            ->first();

        if (!$field) {
            return null;
        }

        $value = CustomFieldValue::where('custom_field_id', $field->id)
            ->where('entity_type', $this->getCustomFieldEntityType())
            ->where('entity_id', $this->id)
            ->first();

        return $value?->value;
    }

    public function setCustomFieldValues(array $values, ?Request $request = null): void
    {
        $customFields = CustomField::forEntity($this->getCustomFieldEntityType())->get();
        $entityType = $this->getCustomFieldEntityType();

        foreach ($customFields as $field) {
            $value = null;

            if ($request && in_array($field->type, ['media', 'document', 'video'])) {
                $file = $request->file('custom_fields.' . $field->slug);
                if ($file?->isValid()) {
                    $value = $file->store('custom-fields/' . $entityType . '/' . $this->id, 'public');
                } else {
                    $value = $values[$field->slug] ?? $this->getCustomFieldValue($field->slug);
                }
            } else {
                $value = $values[$field->slug] ?? null;
            }

            if ($value === null && ! array_key_exists($field->slug, $values)) {
                continue;
            }

            $val = $value;
            if (is_array($val) || is_bool($val)) {
                $val = json_encode($val);
            }
            $val = (string) $val;

            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $field->id,
                    'entity_type' => $this->getCustomFieldEntityType(),
                    'entity_id' => $this->id,
                ],
                ['value' => $val]
            );
        }
    }
}
