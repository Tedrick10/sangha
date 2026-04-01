<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'moderation_mark',
        'full_mark',
        'pass_mark',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'moderation_mark' => 'decimal:2',
            'full_mark' => 'decimal:2',
            'pass_mark' => 'decimal:2',
        ];
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_subject');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
