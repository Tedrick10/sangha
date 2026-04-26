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

    public const STATUS_ELIGIBLE = 'eligible';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_NEEDED_UPDATE = 'needed_update';

    public const STATUS_REJECTED = 'rejected';

    protected static function booted(): void
    {
        static::saving(function (Sangha $sangha): void {
            // When exam changes, clear hall desk — unless this same request assigns a new desk
            // (e.g. admin approves and sets exam + desk together; otherwise approval desk was wiped here).
            if ($sangha->isDirty('exam_id') && ! $sangha->isDirty('desk_number')) {
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
        'workflow_status',
        'eligible_roll_number',
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
        if (in_array($this->workflow_status, [
            self::STATUS_ELIGIBLE,
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_NEEDED_UPDATE,
            self::STATUS_REJECTED,
        ], true)) {
            return $this->workflow_status;
        }

        if ($this->approved) {
            return self::STATUS_APPROVED;
        }

        return filled($this->rejection_reason) ? self::STATUS_REJECTED : self::STATUS_PENDING;
    }
}
