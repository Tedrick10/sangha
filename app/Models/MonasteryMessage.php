<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonasteryMessage extends Model
{
    protected $fillable = [
        'monastery_id',
        'sender_type',
        'user_id',
        'message',
        'payload_json',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function monastery(): BelongsTo
    {
        return $this->belongsTo(Monastery::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

