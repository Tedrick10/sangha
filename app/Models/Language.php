<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = ['name', 'code', 'flag', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function getTranslation(string $key): ?string
    {
        return $this->translations()->where('key', $key)->value('value');
    }

    public function setTranslation(string $key, ?string $value): void
    {
        $this->translations()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isUnofficial(string $code): bool
    {
        return static::where('code', $code)->where('is_active', true)->exists();
    }

    /**
     * Whether the stored flag is a 2-letter country code (for flag image) vs emoji.
     */
    public function isFlagCountryCode(): bool
    {
        $flag = $this->flag ?? '';
        return strlen($flag) === 2 && ctype_alpha($flag);
    }
}
