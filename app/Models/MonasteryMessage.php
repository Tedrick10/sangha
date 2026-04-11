<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonasteryMessage extends Model
{
    public const SENDER_MONASTERY = 'monastery';

    public const SENDER_ADMIN = 'admin';

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

    public static function initialsFromName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $out = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $out .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $out !== '' ? $out : '?';
    }

    /**
     * @return array{id: int, sender_type: string, message: string, created_at: string|null, created_human: string, sender_label: string, sender_initials: string}
     */
    public function toChatPayload(?string $monasteryName = null): array
    {
        $label = $this->sender_type === self::SENDER_ADMIN
            ? (string) ($this->user?->name ?: 'Admin')
            : (string) ($monasteryName ?? $this->monastery?->name ?? 'Monastery');

        return [
            'id' => (int) $this->id,
            'sender_type' => (string) $this->sender_type,
            'message' => (string) $this->message,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_human' => (string) ($this->created_at?->format('M j, Y · H:i') ?? ''),
            'sender_label' => $label,
            'sender_initials' => self::initialsFromName($label),
        ];
    }
}
