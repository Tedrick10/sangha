<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'exam' => 'Exam',
            'exam_type' => 'Exam Type',
        ];
    }

    public static function builtInFields(): array
    {
        return [
            'monastery' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter monastery name'],
                ['name' => 'Username', 'slug' => 'username', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter username'],
                ['name' => 'Password', 'slug' => 'password', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter password'],
                ['name' => 'Region', 'slug' => 'region', 'type' => 'text', 'required' => false, 'placeholder' => 'e.g. Mandalay, Yangon'],
                ['name' => 'City', 'slug' => 'city', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter city'],
                ['name' => 'Address', 'slug' => 'address', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter address'],
                ['name' => 'Phone', 'slug' => 'phone', 'type' => 'text', 'required' => false, 'placeholder' => '09-xxxxxxxxx'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
                ['name' => 'Approved', 'slug' => 'approved', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
            ],
            'sangha' => [
                ['name' => 'Monastery', 'slug' => 'monastery_id', 'type' => 'select', 'required' => true, 'placeholder' => null],
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter name'],
                ['name' => 'Username', 'slug' => 'username', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter username'],
                ['name' => 'Password', 'slug' => 'password', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter password'],
                ['name' => 'Exam', 'slug' => 'exam_id', 'type' => 'select', 'required' => false, 'placeholder' => null],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
                ['name' => 'Approved', 'slug' => 'approved', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
            ],
            'exam' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter exam name'],
                ['name' => 'Exam Date', 'slug' => 'exam_date', 'type' => 'date', 'required' => false, 'placeholder' => null],
                ['name' => 'Exam Type', 'slug' => 'exam_type_id', 'type' => 'select', 'required' => false, 'placeholder' => null],
                ['name' => 'Location', 'slug' => 'location', 'type' => 'text', 'required' => false, 'placeholder' => 'Enter location'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
                ['name' => 'Approved', 'slug' => 'approved', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
            ],
            'exam_type' => [
                ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'required' => true, 'placeholder' => 'Enter exam type name'],
                ['name' => 'Description', 'slug' => 'description', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Enter description'],
                ['name' => 'Active', 'slug' => 'is_active', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
                ['name' => 'Approved', 'slug' => 'approved', 'type' => 'checkbox', 'required' => false, 'placeholder' => null],
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
}
