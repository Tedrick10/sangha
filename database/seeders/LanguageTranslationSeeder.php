<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LanguageTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $keys = self::collectTranslationKeys();
        $languages = Language::query()->where('is_active', true)->get();

        foreach ($languages as $language) {
            $existingValues = $language->translations()->pluck('value', 'key');
            foreach ($keys as $key => $defaultValue) {
                $language->translations()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $language->code === 'en' ? $defaultValue : ($existingValues[$key] ?? $defaultValue)]
                );
            }
        }
    }

    /**
     * Canonical English defaults plus every t('key') discovered in views/app.
     *
     * @return array<string, string>
     */
    public static function collectTranslationKeys(): array
    {
        $keys = config('translation-keys', []);
        $paths = [
            resource_path('views'),
            app_path(),
        ];
        $pattern = "/t\\(\\s*['\"]([^'\"]+)['\"]\\s*(?:,\\s*['\"]([^'\"]*)['\"])?\\s*\\)/";

        foreach ($paths as $path) {
            if (! File::isDirectory($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                $content = File::get($file->getRealPath());
                if (! preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    continue;
                }

                foreach ($matches as $match) {
                    $key = $match[1] ?? null;
                    if (! $key) {
                        continue;
                    }
                    $default = $match[2] ?? ($keys[$key] ?? $key);
                    $keys[$key] = $keys[$key] ?? $default;
                }
            }
        }

        ksort($keys);

        return $keys;
    }
}
