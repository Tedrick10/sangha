<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamType extends Model
{
    use HasFactory, HasCustomFields;

    protected function getCustomFieldEntityType(): string
    {
        return 'exam_type';
    }

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'approved',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ];
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'exam_type_id');
    }
}
