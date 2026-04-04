<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\Monastery;
use App\Models\Sangha;
use App\Models\Website;

class WebsitePagePresentation
{
    public static function theme(string $slug): string
    {
        return match ($slug) {
            'about' => 'split',
            'contact' => 'contact',
            'privacy', 'terms-of-use' => 'legal',
            'faq' => 'faq',
            'accessibility' => 'utility',
            'guidelines' => 'timeline',
            'syllabus' => 'syllabus',
            'news', 'events' => 'magazine',
            'donate', 'volunteer' => 'cta',
            'partners' => 'directory',
            'sitemap' => 'sitemap',
            'gallery' => 'gallery',
            'resources' => 'resources',
            'past-papers' => 'papers',
            'exam-schedule' => 'schedule',
            default => 'standard',
        };
    }

    /**
     * Full Tailwind classes for the top accent bar (must be static strings for Tailwind JIT).
     */
    public static function accentBarClasses(string $slug): string
    {
        return match ($slug) {
            'exam-schedule' => 'from-sky-400 via-blue-500 to-indigo-600 dark:from-sky-500 dark:via-blue-600 dark:to-indigo-500',
            'contact', 'volunteer', 'accessibility' => 'from-teal-400 via-emerald-500 to-teal-600 dark:from-teal-500 dark:via-emerald-600 dark:to-teal-500',
            'privacy', 'terms-of-use', 'faq' => 'from-slate-400 via-slate-500 to-slate-700 dark:from-slate-500 dark:via-slate-400 dark:to-slate-600',
            'donate' => 'from-rose-400 via-amber-500 to-amber-600 dark:from-rose-500 dark:via-amber-500 dark:to-amber-400',
            'news', 'events' => 'from-violet-400 via-fuchsia-500 to-purple-600 dark:from-violet-500 dark:via-fuchsia-500 dark:to-purple-500',
            'partners', 'sitemap' => 'from-cyan-400 via-sky-500 to-blue-600 dark:from-cyan-500 dark:via-sky-500 dark:to-blue-500',
            'past-papers' => 'from-indigo-400 via-indigo-500 to-violet-600 dark:from-indigo-500 dark:via-violet-500 dark:to-indigo-400',
            'resources' => 'from-amber-400 via-orange-500 to-rose-500 dark:from-amber-500 dark:via-orange-500 dark:to-rose-500',
            'gallery' => 'from-stone-400 via-amber-500 to-stone-600 dark:from-stone-500 dark:via-amber-600 dark:to-stone-500',
            default => 'from-amber-400 via-amber-500 to-amber-600 dark:from-amber-600 dark:via-amber-500 dark:to-amber-400',
        };
    }

    public static function isPlaceholderContent(?string $html, string $title): bool
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags($html ?? '')));
        if ($plain === '') {
            return true;
        }
        if (preg_match('/content\s+for\s+.+\s+page\.?$/iu', $plain)) {
            return true;
        }
        $t = trim(preg_replace('/\s+/u', ' ', strip_tags($title)));
        if ($t !== '' && preg_match('/^'.preg_quote($t, '/').'\s+content\s+for\s+/iu', $plain)) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public static function extras(string $slug): array
    {
        $e = [];

        if ($slug === 'sitemap') {
            $e['sitemapPages'] = Website::query()
                ->where('type', 'page')
                ->where('is_published', true)
                ->whereNotIn('slug', ['home', 'login', 'footer', 'registration'])
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get(['slug', 'title', 'sort_order']);
            $e['sitemapSections'] = self::sitemapSections();
        }

        if ($slug === 'partners') {
            $e['partnerMonasteries'] = Monastery::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(60)
                ->get(['name', 'city', 'region', 'address', 'phone']);
        }

        if ($slug === 'events') {
            $e['eventsExams'] = Exam::query()
                ->where('is_active', true)
                ->where('approved', true)
                ->orderByRaw('CASE WHEN exam_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('exam_date')
                ->orderBy('name')
                ->take(10)
                ->with(['monastery', 'examType'])
                ->get();
        }

        if ($slug === 'about') {
            $e['aboutStats'] = [
                'monasteries' => Monastery::query()->where('is_active', true)->count(),
                'exams' => Exam::query()->where('is_active', true)->where('approved', true)->count(),
                'sanghas' => Sangha::query()->where('is_active', true)->count(),
            ];
        }

        return $e;
    }

    /**
     * @return list<array{label_key: string, label_default: string, slugs: list<string>}>
     */
    public static function sitemapSections(): array
    {
        return [
            [
                'label_key' => 'sitemap_section_exams',
                'label_default' => 'Examinations',
                'slugs' => ['exam-schedule', 'guidelines', 'syllabus', 'past-papers'],
            ],
            [
                'label_key' => 'sitemap_section_about',
                'label_default' => 'About & media',
                'slugs' => ['about', 'contact', 'partners', 'news', 'events', 'gallery'],
            ],
            [
                'label_key' => 'sitemap_section_legal',
                'label_default' => 'Policies & help',
                'slugs' => ['privacy', 'terms-of-use', 'faq', 'accessibility', 'resources'],
            ],
            [
                'label_key' => 'sitemap_section_community',
                'label_default' => 'Community',
                'slugs' => ['donate', 'volunteer', 'sitemap'],
            ],
        ];
    }
}
