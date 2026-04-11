<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
    protected $fillable = [
        'entity_type',
        'name',
        'slug',
        'type',
        'options',
        'required',
        'placeholder',
        'sort_order',
        'is_built_in',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'is_built_in' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    public static function entityTypes(): array
    {
        return [
            'monastery' => 'Monastery',
            'sangha' => 'Sangha',
            'request' => 'Request Form',
            'monastery_exam' => 'Monastery exam',
            'exam' => 'Exam',
            'exam_type' => 'Exam Type',
        ];
    }

    public static function builtInFields(): array
    {
        return [
            'monastery' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter monastery name'],
                ['name' => 'Student Id', 'slug' => 'username', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter student id'],
                ['name' => 'Password', 'slug' => 'password', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter password'],
                ['name' => 'Region', 'slug' => 'region', 'type' => 'text', 'required' => false, 'placeholder' => 'e.g. Mandalay, Yangon'],
                ['name' => 'City', 'slug' => 'city', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter city'],
                ['name' => 'Address', 'slug' => 'address', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter address'],
                ['name' => 'Phone', 'slug' => 'phone', 'type' => 'text', 'required' => false, 'placeholder' => '09-xxxxxxxxx'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
            ],
            'sangha' => [
                ['name' => 'Monastery', 'slug' => 'monastery_id', 'type' => 'select', 'required' => true, 'placeholder' => null],
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter name'],
                ['name' => 'Father name', 'slug' => 'father_name', 'type' => 'text', 'required' => false, 'placeholder' => 'Optional'],
                ['name' => 'NRC number', 'slug' => 'nrc_number', 'type' => 'text', 'required' => false, 'placeholder' => 'Optional'],
                ['name' => 'Student Id', 'slug' => 'username', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter student id'],
                ['name' => 'Exam', 'slug' => 'exam_id', 'type' => 'select', 'required' => false, 'placeholder' => null],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
            ],
            'monastery_exam' => [],
            'exam' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter exam name'],
                ['name' => 'Exam Date', 'slug' => 'exam_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Exam Type', 'slug' => 'exam_type_id', 'type' => 'select', 'required' => false, 'placeholder' => null],
                ['name' => 'Location', 'slug' => 'location', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter location'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
            ],
            'exam_type' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter exam type name'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
            ],
        ];
    }

    public function scopeForEntity(Builder $query, string $entityType): Builder
    {
        return $query->where('entity_type', $entityType)->orderBy('sort_order')->orderBy('name');
    }

    public static function fieldTypes(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'number' => 'Number',
            'date' => 'Date',
            'time' => 'Time',
            'datetime' => 'Date & Time',
            'select' => 'Select',
            'checkbox' => 'Checkbox',
            'media' => 'Media (image/file)',
            'document' => 'Document (file)',
            'video' => 'Video',
        ];
    }

    /**
     * Sangha custom_field rows (built-in + extra), keyed by slug — for labels / placeholders / validation.
     */
    public static function sanghaDefinitionsBySlug(): Collection
    {
        return static::forEntity('sangha')->get()->keyBy('slug');
    }

    public static function sanghaSlugRequired(Collection $bySlug, string $slug): bool
    {
        $cf = $bySlug->get($slug);
        if ($cf) {
            return (bool) $cf->required;
        }

        foreach (self::builtInFields()['sangha'] ?? [] as $row) {
            if (($row['slug'] ?? null) === $slug) {
                return (bool) ($row['required'] ?? false);
            }
        }

        return false;
    }

    /**
     * Validation for sangha columns that map to built-in custom fields (not custom_field_values).
     *
     * @param  array<int, string>  $onlyKeys  e.g. ['monastery_id','name',...]
     * @param  'any'|'active_only'  $monasteryExistsConstraint  public registration uses active monasteries only
     * @return array<string, array<int, mixed>>
     */
    public static function sanghaCoreValidationRules(
        Collection $bySlug,
        array $onlyKeys,
        ?int $ignoreSanghaIdForUsername = null,
        string $monasteryExistsConstraint = 'any'
    ): array {
        $req = fn (string $slug) => self::sanghaSlugRequired($bySlug, $slug);
        $rules = [];

        if (in_array('monastery_id', $onlyKeys, true)) {
            $base = $req('monastery_id') ? ['required'] : ['nullable'];
            if ($monasteryExistsConstraint === 'active_only') {
                $base[] = Rule::exists('monasteries', 'id')->where(fn ($q) => $q->where('is_active', true));
            } else {
                $base[] = 'exists:monasteries,id';
            }
            $rules['monastery_id'] = $base;
        }

        if (in_array('name', $onlyKeys, true)) {
            $rules['name'] = $req('name')
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'];
        }

        if (in_array('father_name', $onlyKeys, true)) {
            $rules['father_name'] = $req('father_name')
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'];
        }

        if (in_array('nrc_number', $onlyKeys, true)) {
            $rules['nrc_number'] = $req('nrc_number')
                ? ['required', 'string', 'max:100']
                : ['nullable', 'string', 'max:100'];
        }

        if (in_array('username', $onlyKeys, true)) {
            $unique = Rule::unique('sanghas', 'username');
            if ($ignoreSanghaIdForUsername !== null) {
                $unique = $unique->ignore($ignoreSanghaIdForUsername);
            }
            if ($req('username')) {
                $rules['username'] = ['required', 'string', 'max:255', $unique];
            } else {
                $rules['username'] = ['nullable', 'string', 'max:255', $unique];
            }
        }

        if (in_array('exam_id', $onlyKeys, true)) {
            $rules['exam_id'] = $req('exam_id')
                ? ['required', 'exists:exams,id']
                : ['nullable', 'exists:exams,id'];
        }

        if (in_array('description', $onlyKeys, true)) {
            $rules['description'] = $req('description')
                ? ['required', 'string']
                : ['nullable', 'string'];
        }

        return $rules;
    }
}
