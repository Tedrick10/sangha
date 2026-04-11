<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Monastery extends Model implements AuthenticatableContract
{
    use Authenticatable, HasCustomFields, HasFactory, Notifiable;

    protected function getCustomFieldEntityType(): string
    {
        return 'monastery';
    }

    protected $fillable = [
        'name',
        'username',
        'password',
        'address',
        'phone',
        'region',
        'city',
        'description',
        'is_active',
        'approved',
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

    public function sanghas(): HasMany
    {
        return $this->hasMany(Sangha::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MonasteryMessage::class);
    }

    public function formRequests(): HasMany
    {
        return $this->hasMany(MonasteryFormRequest::class);
    }

    public function moderationStatus(): string
    {
        if ($this->approved) {
            return 'approved';
        }

        return filled($this->rejection_reason) ? 'rejected' : 'pending';
    }
}
