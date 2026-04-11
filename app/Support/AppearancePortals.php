<?php

namespace App\Support;

use App\Models\SiteSetting;

final class AppearancePortals
{
    public const SETTING_KEY = 'appearance_portals';

    /** @var list<string> */
    private const FIELDS = [
        'button_bg',
        'button_text',
        'body_text',
        'muted_text',
        'page_bg',
        'surface_bg',
        'body_text_dark',
        'muted_text_dark',
    ];

    /** @var array<string, array<string, string>> */
    private const DEFAULTS = [
        'website' => [
            'button_bg' => '#eab308',
            'button_text' => '#ffffff',
            'body_text' => '#1c1917',
            'muted_text' => '#57534e',
            'page_bg' => '#fafaf9',
            'surface_bg' => '#ffffff',
            'body_text_dark' => '#e7e5e4',
            'muted_text_dark' => '#a8a29e',
        ],
        'admin' => [
            'button_bg' => '#d97706',
            'button_text' => '#ffffff',
            'body_text' => '#1c1917',
            'muted_text' => '#57534e',
            'page_bg' => '#fafaf9',
            'surface_bg' => '#ffffff',
            'body_text_dark' => '#e2e8f0',
            'muted_text_dark' => '#94a3b8',
        ],
        'monastery' => [
            'button_bg' => '#d97706',
            'button_text' => '#ffffff',
            'body_text' => '#0f172a',
            'muted_text' => '#64748b',
            'page_bg' => '#f8fafc',
            'surface_bg' => '#ffffff',
            'body_text_dark' => '#f1f5f9',
            'muted_text_dark' => '#94a3b8',
        ],
    ];

    private const PORTALS = ['website', 'admin', 'monastery'];

    /**
     * @return list<string>
     */
    public static function fields(): array
    {
        return self::FIELDS;
    }

    /**
     * @return array<string, array<string, array<string, string>>>
     */
    public static function presets(): array
    {
        return [
            'website' => [
                'sun' => [
                    'button_bg' => '#eab308',
                    'button_text' => '#ffffff',
                    'body_text' => '#1c1917',
                    'muted_text' => '#57534e',
                    'page_bg' => '#fffbeb',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#fef3c7',
                    'muted_text_dark' => '#fcd34d',
                ],
                'forest' => [
                    'button_bg' => '#16a34a',
                    'button_text' => '#ffffff',
                    'body_text' => '#14532d',
                    'muted_text' => '#3f6212',
                    'page_bg' => '#f0fdf4',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#bbf7d0',
                    'muted_text_dark' => '#86efac',
                ],
                'ocean' => [
                    'button_bg' => '#0284c7',
                    'button_text' => '#ffffff',
                    'body_text' => '#0c4a6e',
                    'muted_text' => '#0369a1',
                    'page_bg' => '#f0f9ff',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#bae6fd',
                    'muted_text_dark' => '#7dd3fc',
                ],
            ],
            'admin' => [
                'ember' => [
                    'button_bg' => '#ea580c',
                    'button_text' => '#ffffff',
                    'body_text' => '#431407',
                    'muted_text' => '#9a3412',
                    'page_bg' => '#fff7ed',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#fed7aa',
                    'muted_text_dark' => '#fdba74',
                ],
                'slate' => [
                    'button_bg' => '#334155',
                    'button_text' => '#ffffff',
                    'body_text' => '#0f172a',
                    'muted_text' => '#64748b',
                    'page_bg' => '#f8fafc',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#e2e8f0',
                    'muted_text_dark' => '#94a3b8',
                ],
                'violet' => [
                    'button_bg' => '#7c3aed',
                    'button_text' => '#ffffff',
                    'body_text' => '#1e1b4b',
                    'muted_text' => '#5b21b6',
                    'page_bg' => '#f5f3ff',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#ddd6fe',
                    'muted_text_dark' => '#c4b5fd',
                ],
            ],
            'monastery' => [
                'amber' => self::DEFAULTS['monastery'],
                'teal' => [
                    'button_bg' => '#0d9488',
                    'button_text' => '#ffffff',
                    'body_text' => '#134e4a',
                    'muted_text' => '#0f766e',
                    'page_bg' => '#f0fdfa',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#99f6e4',
                    'muted_text_dark' => '#5eead4',
                ],
                'indigo' => [
                    'button_bg' => '#4f46e5',
                    'button_text' => '#ffffff',
                    'body_text' => '#1e1b4b',
                    'muted_text' => '#4338ca',
                    'page_bg' => '#eef2ff',
                    'surface_bg' => '#ffffff',
                    'body_text_dark' => '#c7d2fe',
                    'muted_text_dark' => '#a5b4fc',
                ],
            ],
        ];
    }

    /**
     * @return array{button_bg: string, button_text: string, body_text: string, muted_text: string, page_bg: string, surface_bg: string, body_text_dark: string, muted_text_dark: string}
     */
    public static function defaults(string $name): array
    {
        return self::DEFAULTS[$name] ?? self::DEFAULTS['admin'];
    }

    /**
     * Effective palette for previews (saved overrides merged with defaults).
     *
     * @return array<string, string>
     */
    public static function mergedPalette(string $name): array
    {
        $saved = self::portal($name);
        $defaults = self::defaults($name);
        $out = [];
        foreach (self::FIELDS as $k) {
            $out[$k] = $saved[$k] !== '' ? $saved[$k] : $defaults[$k];
        }

        return $out;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function raw(): array
    {
        $json = SiteSetting::get(self::SETTING_KEY);
        $decoded = json_decode((string) $json, true);
        if (! is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    /**
     * Sanitized saved values only (empty string = not set).
     *
     * @return array<string, string>
     */
    public static function portal(string $name): array
    {
        $raw = self::raw();
        $p = $raw[$name] ?? [];
        if (! is_array($p)) {
            $p = [];
        }
        $out = [];
        foreach (self::FIELDS as $k) {
            $v = isset($p[$k]) ? (string) $p[$k] : '';
            $out[$k] = self::sanitizeHex($v) ?? '';
        }

        return $out;
    }

    public static function isPortalActive(string $name): bool
    {
        foreach (self::portal($name) as $v) {
            if ($v !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Merged defaults for CSS variables when a portal has any customization.
     *
     * @return array<string, string>|null
     */
    public static function resolvedForCss(string $name): ?array
    {
        if (! self::isPortalActive($name)) {
            return null;
        }
        $saved = self::portal($name);
        $defaults = self::defaults($name);
        $resolved = [];
        foreach (self::FIELDS as $k) {
            $resolved[$k] = $saved[$k] !== '' ? $saved[$k] : $defaults[$k];
        }

        return $resolved;
    }

    public static function inlineStyle(string $name): string
    {
        $r = self::resolvedForCss($name);
        if ($r === null) {
            return '';
        }

        return sprintf(
            '--ap-btn-bg:%1$s;--ap-btn-text:%2$s;--ap-body:%3$s;--ap-muted:%4$s;--ap-page-bg:%5$s;--ap-surface-bg:%6$s;--ap-body-dark:%7$s;--ap-muted-dark:%8$s;',
            $r['button_bg'],
            $r['button_text'],
            $r['body_text'],
            $r['muted_text'],
            $r['page_bg'],
            $r['surface_bg'],
            $r['body_text_dark'],
            $r['muted_text_dark'],
        );
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    public static function normalizePortalInput(array $input): array
    {
        $out = [];
        foreach (self::FIELDS as $k) {
            $v = isset($input[$k]) ? trim((string) $input[$k]) : '';
            $out[$k] = self::sanitizeHex($v) ?? '';
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $portals
     * @return array<string, array<string, string>>
     */
    public static function normalizeAllFromRequest(array $portals): array
    {
        $stored = [];
        foreach (self::PORTALS as $portal) {
            $row = isset($portals[$portal]) && is_array($portals[$portal])
                ? self::normalizePortalInput($portals[$portal])
                : self::normalizePortalInput([]);
            if (array_filter($row)) {
                $stored[$portal] = $row;
            }
        }

        return $stored;
    }

    public static function saveFromRequest(array $portals): void
    {
        $stored = self::normalizeAllFromRequest($portals);
        if ($stored === []) {
            SiteSetting::where('key', self::SETTING_KEY)->delete();

            return;
        }
        SiteSetting::set(self::SETTING_KEY, json_encode($stored));
    }

    public static function sanitizeHex(?string $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $v) === 1) {
            return strtolower($v);
        }

        return null;
    }
}
