<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function imageKeys(): array
    {
        return [
            'logo' => ['label' => 'Logo', 'hint' => 'Site logo (PNG, SVG, JPG). Recommended: 200×50px or similar.'],
            'favicon' => ['label' => 'Favicon', 'hint' => 'Browser tab icon. Recommended: 32×32px or 64×64px (ICO, PNG).'],
            'og_image' => ['label' => 'OG Image', 'hint' => 'Social sharing preview image. Recommended: 1200×630px (PNG, JPG).'],
            'apple_touch_icon' => ['label' => 'Apple Touch Icon', 'hint' => 'Icon for iOS home screen. Recommended: 180×180px (PNG).'],
        ];
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get the public URL for an image setting, or null if not set.
     */
    public static function imageUrl(string $key): ?string
    {
        $path = static::get($key);
        if (!$path) {
            return null;
        }
        return Storage::disk('public')->exists($path)
            ? Storage::disk('public')->url($path)
            : null;
    }
}
