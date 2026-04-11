<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sangha extends Model implements AuthenticatableContract
{
    use Authenticatable, HasCustomFields, HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Sangha $sangha): void {
            if ($sangha->isDirty('exam_id')) {
                $sangha->desk_number = null;
            }
        });
    }

    protected function getCustomFieldEntityType(): string
    {
        return 'sangha';
    }

    protected $fillable = [
        'monastery_id',
        'exam_id',
        'desk_number',
        'name',
        'father_name',
        'nrc_number',
        'username',
        'password',
        'type',
        'description',
        'is_active',
        'approved',
        'top20_position',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function monastery(): BelongsTo
    {
        return $this->belongsTo(Monastery::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function moderationStatus(): string
    {
        if ($this->approved) {
            return 'approved';
        }

        return filled($this->rejection_reason) ? 'rejected' : 'pending';
    }
}
