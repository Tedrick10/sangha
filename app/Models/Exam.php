<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory, HasCustomFields;

    protected $fillable = [
        'name',
        'description',
        'exam_date',
        'exam_type_id',
        'monastery_id',
        'location',
        'is_active',
        'approved',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ];
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function monastery(): BelongsTo
    {
        return $this->belongsTo(Monastery::class);
    }

    public function sanghas(): HasMany
    {
        return $this->hasMany(Sangha::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'exam_subject');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    protected function getCustomFieldEntityType(): string
    {
        return 'exam';
    }
}
