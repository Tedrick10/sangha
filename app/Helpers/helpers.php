<?php

use App\Models\Language;

if (! function_exists('t')) {
    /**
     * Get translated text. Uses unofficial language translations from DB when locale matches.
     */
    function t(string $key, ?string $default = null): string
    {
        $keys = config('translation-keys', []);
        $default = $default ?? ($keys[$key] ?? $key);

        if (! Language::isUnofficial(app()->getLocale())) {
            return $default;
        }

        $translation = \App\Models\Translation::whereHas('language', fn ($q) => $q->where('code', app()->getLocale()))
            ->where('key', $key)
            ->value('value');

        return $translation ?? $default;
    }
}

if (! function_exists('sync_app_theme')) {
    /**
     * Persist appearance for public site, admin, and monastery (single session preference).
     */
    function sync_app_theme(string $theme): void
    {
        if (! in_array($theme, ['light', 'dark', 'system'], true)) {
            return;
        }
        session([
            'app_theme' => $theme,
            'website_theme' => $theme,
            'admin_theme' => $theme,
        ]);
    }
}

if (! function_exists('sync_app_locale')) {
    /**
     * Persist language for public site, admin, and monastery (single session preference).
     */
    function sync_app_locale(string $code): void
    {
        session([
            'app_locale' => $code,
            'website_locale' => $code,
            'admin_locale' => $code,
        ]);
    }
}

if (! function_exists('resolved_app_theme')) {
    function resolved_app_theme(): string
    {
        $t = session('app_theme') ?? session('website_theme') ?? session('admin_theme');

        return in_array($t, ['light', 'dark', 'system'], true) ? $t : 'system';
    }
}

if (! function_exists('admin_per_page')) {
    /**
     * Valid per-page value for admin tables (from request or default).
     */
    function admin_per_page(int $default = 15): int
    {
        $allowed = [10, 15, 25, 50, 100];
        $perPage = request()->integer('per_page', $default);
        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }
}
