<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
    /** JSON map in {@see SiteSetting}: entity_type => list of built-in slugs admins removed (sync will not recreate). */
    public const SUPPRESSED_BUILTINS_SITE_KEY = 'custom_field_suppressed_builtins';

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
            'request' => 'Transfer',
            'exam' => 'Exam',
            'programme_primary' => 'Primary',
            'programme_intermediate' => 'Intermediate',
            'programme_level_1' => 'Level 1',
            'programme_level_2' => 'Level 2',
            'programme_level_3' => 'Level 3',
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
            'request' => [
                ['name' => 'From (monastery)', 'slug' => 'transfer_from', 'type' => 'text', 'required' => false, 'placeholder' => 'Filled automatically from your monastery account'],
                ['name' => 'To (destination)', 'slug' => 'transfer_to', 'type' => 'monastery_select', 'required' => true, 'placeholder' => 'Select destination monastery'],
                [
                    'name' => 'Sangha to transfer',
                    'slug' => 'transfer_sangha_id',
                    'type' => 'approved_sangha',
                    'required' => true,
                    'placeholder' => 'Select an approved sangha member',
                ],
                ['name' => 'Transfer date', 'slug' => 'transfer_date', 'type' => 'date', 'required' => true, 'placeholder' => null],
                ['name' => 'Additional details', 'slug' => 'transfer_details', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Optional: other notes for the secretariat'],
                ['name' => 'Supporting document', 'slug' => 'transfer_attachment', 'type' => 'document', 'required' => false, 'placeholder' => 'Optional PDF or scan'],
            ],
            'monastery_exam' => [
                [
                    'name' => 'Approved student',
                    'slug' => 'approved_sangha_id',
                    'type' => 'approved_sangha',
                    'required' => false,
                    'placeholder' => 'Select an approved student',
                ],
                [
                    'name' => 'Year',
                    'slug' => 'exam_year',
                    'type' => 'select',
                    'required' => false,
                    'placeholder' => 'Select year',
                    'options' => ['2024', '2025', '2026'],
                ],
                [
                    'name' => 'Exam',
                    'slug' => 'exam_session',
                    'type' => 'dependent_select',
                    'required' => false,
                    'placeholder' => 'Select exam',
                    'options' => [
                        '2024' => ['မူလတန်း စာမေးပွဲ (၂၀၂၄)', 'ဒုတိယတန်း စာမေးပွဲ (၂၀၂၄)'],
                        '2025' => ['မူလတန်း စာမေးပွဲ (၂၀၂၅)'],
                        '2026' => ['မူလတန်း စာမေးပွဲ (၂၀၂၆)', 'ဒုတိယတန်း စာမေးပွဲ (၂၀၂၆)'],
                    ],
                ],
            ],
            'exam' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter exam name'],
                ['name' => 'Exam Date', 'slug' => 'exam_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Exam Type', 'slug' => 'exam_type_id', 'type' => 'select', 'required' => true, 'placeholder' => null],
                ['name' => 'Location', 'slug' => 'location', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter location'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
            ],
            'programme_primary' => [
                ['name' => 'Gender', 'slug' => 'gender', 'type' => 'select', 'required' => false, 'placeholder' => 'Select gender', 'options' => ['Male', 'Female']],
                self::programmeLevelInformationDefinition(),
                ['name' => 'Ordination Date', 'slug' => 'ordination_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Teacher Name', 'slug' => 'teacher_name', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter teacher name'],
                ['name' => 'Notes', 'slug' => 'notes', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter notes'],
                ['name' => 'Supporting document', 'slug' => 'supporting_document', 'type' => 'document', 'required' => false, 'placeholder' => 'Upload supporting document'],
            ],
            'programme_intermediate' => [
                ['name' => 'Gender', 'slug' => 'gender', 'type' => 'select', 'required' => false, 'placeholder' => 'Select gender', 'options' => ['Male', 'Female']],
                self::programmeLevelInformationDefinition(),
                ['name' => 'Ordination Date', 'slug' => 'ordination_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Teacher Name', 'slug' => 'teacher_name', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter teacher name'],
                ['name' => 'Notes', 'slug' => 'notes', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter notes'],
                ['name' => 'Supporting document', 'slug' => 'supporting_document', 'type' => 'document', 'required' => false, 'placeholder' => 'Upload supporting document'],
            ],
            'programme_level_1' => [
                ['name' => 'Gender', 'slug' => 'gender', 'type' => 'select', 'required' => false, 'placeholder' => 'Select gender', 'options' => ['Male', 'Female']],
                self::programmeLevelInformationDefinition(),
                ['name' => 'Ordination Date', 'slug' => 'ordination_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Teacher Name', 'slug' => 'teacher_name', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter teacher name'],
                ['name' => 'Notes', 'slug' => 'notes', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter notes'],
                ['name' => 'Supporting document', 'slug' => 'supporting_document', 'type' => 'document', 'required' => false, 'placeholder' => 'Upload supporting document'],
            ],
            'programme_level_2' => [
                ['name' => 'Gender', 'slug' => 'gender', 'type' => 'select', 'required' => false, 'placeholder' => 'Select gender', 'options' => ['Male', 'Female']],
                self::programmeLevelInformationDefinition(),
                ['name' => 'Ordination Date', 'slug' => 'ordination_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Teacher Name', 'slug' => 'teacher_name', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter teacher name'],
                ['name' => 'Notes', 'slug' => 'notes', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter notes'],
                ['name' => 'Supporting document', 'slug' => 'supporting_document', 'type' => 'document', 'required' => false, 'placeholder' => 'Upload supporting document'],
            ],
            'programme_level_3' => [
                ['name' => 'Gender', 'slug' => 'gender', 'type' => 'select', 'required' => false, 'placeholder' => 'Select gender', 'options' => ['Male', 'Female']],
                self::programmeLevelInformationDefinition(),
                ['name' => 'Ordination Date', 'slug' => 'ordination_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Teacher Name', 'slug' => 'teacher_name', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter teacher name'],
                ['name' => 'Notes', 'slug' => 'notes', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter notes'],
                ['name' => 'Supporting document', 'slug' => 'supporting_document', 'type' => 'document', 'required' => false, 'placeholder' => 'Upload supporting document'],
            ],
        ];
    }

    public function scopeForEntity(Builder $query, string $entityType): Builder
    {
        return $query->where('entity_type', $entityType)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Built-in fields the admin may not delete (exam portal / data integrity).
     */
    public static function builtInDeleteForbidden(CustomField $field): bool
    {
        if (! $field->is_built_in || $field->entity_type !== 'monastery_exam') {
            return false;
        }

        return in_array($field->slug, ['approved_sangha_id', 'exam_year', 'exam_session'], true);
    }

    /**
     * Parent custom field slug for a dependent select (monastery exam form).
     */
    public static function dependentSelectParentSlug(CustomField $field): ?string
    {
        if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session') {
            return 'exam_year';
        }

        return null;
    }

    /**
     * Normalize JSON-decoded map: year string => list of exam labels.
     *
     * @param  array<mixed, mixed>  $decoded
     * @return array<string, list<string>>
     */
    public static function normalizeDependentExamOptionsMap(array $decoded): array
    {
        $out = [];
        foreach ($decoded as $year => $exams) {
            $y = is_string($year) || is_int($year) ? trim((string) $year) : '';
            if ($y === '') {
                continue;
            }
            if (! is_array($exams)) {
                $exams = [$exams];
            }
            $list = [];
            foreach ($exams as $e) {
                if (is_string($e) || is_numeric($e)) {
                    $s = trim((string) $e);
                    if ($s !== '') {
                        $list[] = $s;
                    }
                }
            }
            $out[$y] = array_values(array_unique($list));
        }

        return $out;
    }

    public static function canDeleteInAdmin(CustomField $field): bool
    {
        return ! static::builtInDeleteForbidden($field);
    }

    /**
     * @return array<string, list<string>>
     */
    public static function suppressedBuiltInSlugsByEntity(): array
    {
        $raw = SiteSetting::get(self::SUPPRESSED_BUILTINS_SITE_KEY);
        if (! filled($raw)) {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public static function isBuiltInSlugSuppressed(string $entityType, string $slug): bool
    {
        $map = static::suppressedBuiltInSlugsByEntity();

        return in_array($slug, $map[$entityType] ?? [], true);
    }

    public static function suppressBuiltInSlug(string $entityType, string $slug): void
    {
        $map = static::suppressedBuiltInSlugsByEntity();
        $list = $map[$entityType] ?? [];
        if (! in_array($slug, $list, true)) {
            $list[] = $slug;
        }
        $map[$entityType] = array_values(array_unique($list));
        SiteSetting::set(self::SUPPRESSED_BUILTINS_SITE_KEY, json_encode($map));
    }

    /**
     * Remove a slug from the suppression map (used when a portal built-in row is missing and must be recreated).
     */
    public static function unsuppressBuiltInSlug(string $entityType, string $slug): void
    {
        $map = static::suppressedBuiltInSlugsByEntity();
        $list = array_values(array_filter($map[$entityType] ?? [], fn ($s) => $s !== $slug));
        if ($list === []) {
            unset($map[$entityType]);
        } else {
            $map[$entityType] = $list;
        }
        SiteSetting::set(self::SUPPRESSED_BUILTINS_SITE_KEY, json_encode($map));
    }

    /**
     * Ensure rows exist for every {@see builtInFields()} definition (admin Custom Fields + monastery request tab).
     * New rows get defaults from code; existing rows keep admin-edited label, type, placeholder, required, sort order.
     */
    public static function syncBuiltInFieldDefinitions(): void
    {
        foreach (self::builtInFields() as $entityType => $fields) {
            foreach (array_values($fields) as $sortOrder => $def) {
                $slug = $def['slug'] ?? '';
                if (static::isBuiltInSlugSuppressed($entityType, $slug)) {
                    if (! in_array($entityType, ['request', 'monastery_exam'], true)) {
                        continue;
                    }
                    if (static::query()->where('entity_type', $entityType)->where('slug', $slug)->exists()) {
                        continue;
                    }
                    static::unsuppressBuiltInSlug($entityType, $slug);
                }

                $field = static::firstOrNew(
                    [
                        'entity_type' => $entityType,
                        'slug' => $def['slug'],
                    ]
                );

                if (! $field->exists) {
                    $field->fill([
                        'name' => $def['name'],
                        'type' => $def['type'],
                        'required' => $def['required'],
                        'placeholder' => $def['placeholder'] ?? null,
                        'sort_order' => $sortOrder,
                        'is_built_in' => true,
                        'options' => array_key_exists('options', $def) ? $def['options'] : null,
                    ]);
                    $field->save();
                } else {
                    if (! $field->is_built_in) {
                        $field->update(['is_built_in' => true]);
                    }
                    if ($field->is_built_in && (int) $field->sort_order !== (int) $sortOrder) {
                        $field->update(['sort_order' => $sortOrder]);
                    }
                    if ($field->is_built_in && $entityType === 'request' && in_array(($def['slug'] ?? ''), ['transfer_to', 'transfer_sangha_id'], true)) {
                        $sync = [];
                        if (($def['type'] ?? null) !== null && $field->type !== $def['type']) {
                            $sync['type'] = $def['type'];
                            $sync['options'] = null;
                        }
                        if (array_key_exists('placeholder', $def) && $field->placeholder !== ($def['placeholder'] ?? null)) {
                            $sync['placeholder'] = $def['placeholder'] ?? null;
                        }
                        if ($sync !== []) {
                            $field->update($sync);
                        }
                    }
                    if ($field->is_built_in && $entityType === 'monastery_exam' && ($def['slug'] ?? '') === 'approved_sangha_id') {
                        $sync = [];
                        if (($def['type'] ?? null) !== null && $field->type !== $def['type']) {
                            $sync['type'] = $def['type'];
                            $sync['options'] = null;
                        }
                        if (array_key_exists('placeholder', $def) && $field->placeholder !== ($def['placeholder'] ?? null)) {
                            $sync['placeholder'] = $def['placeholder'] ?? null;
                        }
                        if ($sync !== []) {
                            $field->update($sync);
                        }
                    }
                }
            }
        }

        foreach (self::builtInFields() as $entityType => $fields) {
            $slugs = collect($fields)->pluck('slug')->filter()->values()->all();
            static::query()
                ->where('entity_type', $entityType)
                ->where('is_built_in', true)
                ->whereNotIn('slug', $slugs)
                ->get()
                ->each(fn (CustomField $obsolete) => $obsolete->delete());
        }
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
            'approved_sangha' => 'Approved student (this monastery)',
            'monastery_select' => 'Monastery (dropdown)',
            'dependent_select' => 'Select (options depend on Year)',
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
        if (static::isBuiltInSlugSuppressed('sangha', $slug)) {
            return false;
        }

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

    /**
     * Built-in "Level" field for programme-specific Sangha forms (monastery portal + admin).
     *
     * @return array{name: string, slug: string, type: string, required: bool, placeholder: string, options: list<string>}
     */
    private static function programmeLevelInformationDefinition(): array
    {
        return [
            'name' => 'Level',
            'slug' => 'level_information',
            'type' => 'select',
            'required' => false,
            'placeholder' => 'Select level',
            'options' => ['Primary', 'Intermediate', 'Level 1', 'Level 2', 'Level 3'],
        ];
    }
}
