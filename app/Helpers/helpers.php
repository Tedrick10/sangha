<?php

use App\Models\Language;
use App\Models\Translation;
use App\Support\AppearancePortals;

if (! function_exists('t')) {
    /**
     * Get translated text. Uses unofficial language translations from DB when locale matches.
     */
    function t(string $key, ?string $default = null, array $replace = []): string
    {
        $keys = config('translation-keys', []);
        $default = $default ?? ($keys[$key] ?? $key);

        $locale = app()->getLocale();

        if (! Language::isUnofficial($locale)) {
            return $replace !== [] ? strtr($default, $replace) : $default;
        }

        $translation = Translation::whereHas('language', fn ($q) => $q->where('code', $locale))
            ->where('key', $key)
            ->value('value');

        if (filled($translation)) {
            return $replace !== [] ? strtr($translation, $replace) : $translation;
        }

        $fileFallback = config('translation-fallbacks.'.$locale.'.'.$key);
        $text = $fileFallback ?? $default;

        return $replace !== [] ? strtr($text, $replace) : $text;
    }
}

if (! function_exists('app_brand_title_lines')) {
    /**
     * Split the site title for display when it ends with "စာမေးပွဲ".
     * Long compound names (e.g. …သနာလင်္ကာရ…) are split before "လင်္ကာ" so lines read naturally in the sidebar/header.
     *
     * @return array<int, string>
     */
    function app_brand_title_lines(?string $name = null): array
    {
        $name = trim((string) ($name ?? config('app.name')));
        if ($name === '') {
            return [''];
        }

        $suffix = 'စာမေးပွဲ';
        if (! str_ends_with($name, $suffix)) {
            return [$name];
        }

        $before = mb_substr($name, 0, mb_strlen($name) - mb_strlen($suffix));
        $before = rtrim($before);
        if ($before === '') {
            return [$name];
        }

        $marker = 'လင်္ကာ';
        $pos = mb_strpos($before, $marker);
        if ($pos !== false && $pos > 0) {
            $part1 = rtrim(mb_substr($before, 0, $pos));
            $part2 = ltrim(mb_substr($before, $pos));
            if ($part1 !== '' && $part2 !== '') {
                return [$part1, $part2, $suffix];
            }
        }

        return [$before, $suffix];
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

if (! function_exists('format_number_display')) {
    /**
     * Format a numeric score/mark for display: whole numbers without ".00", decimals without trailing zeros.
     */
    function format_number_display(mixed $value, ?string $emptyDisplay = null): string
    {
        if ($value === null || $value === '') {
            return $emptyDisplay ?? '';
        }
        if (! is_numeric($value)) {
            return (string) $value;
        }
        $f = (float) $value;
        if (! is_finite($f)) {
            return $emptyDisplay ?? '';
        }
        if (abs($f - round($f)) < 1e-9) {
            return (string) (int) round($f);
        }

        $s = rtrim(rtrim(number_format($f, 10, '.', ''), '0'), '.');

        return $s === '-0' ? '0' : $s;
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

if (! function_exists('appearance_portal_body_attrs')) {
    /**
     * Optional classes + inline CSS variables for customized portal colors (website, admin, monastery).
     *
     * @return array{class: string, style: string}
     */
    function appearance_portal_body_attrs(string $portal): array
    {
        if (! in_array($portal, ['website', 'admin', 'monastery'], true)) {
            return ['class' => '', 'style' => ''];
        }
        if (! AppearancePortals::isPortalActive($portal)) {
            return ['class' => '', 'style' => ''];
        }
        $style = AppearancePortals::inlineStyle($portal);

        return [
            'class' => 'appearance-portal appearance-portal--'.$portal,
            'style' => $style,
        ];
    }
}
