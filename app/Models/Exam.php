<?php

namespace App\Models;

use App\Support\ExamEligibleSnapshot;
use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasCustomFields, HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (Exam $exam): void {
            Sangha::where('exam_id', $exam->id)->update(['desk_number' => null]);
            ExamEligibleSnapshot::removeForExam($exam->id);
        });
    }

    protected $fillable = [
        'name',
        'description',
        'exam_date',
        'exam_type_id',
        'monastery_id',
        'location',
        'is_active',
        'approved',
        'desk_number_prefix',
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

    /** Registered for this exam but not yet assigned a hall desk (entrance tab). */
    public function sanghasPendingEntrance(): HasMany
    {
        return $this->hasMany(Sangha::class)->whereNull('desk_number');
    }

    /** Confirmed for entrance with an assigned desk number (approved tab). */
    public function sanghasSeated(): HasMany
    {
        return $this->hasMany(Sangha::class)->whereNotNull('desk_number')->orderBy('desk_number');
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
