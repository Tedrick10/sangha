<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

/**
 * Fills translations for every active language by merging canonical English keys
 * with optional per-locale overrides in config/demo-localization/{code}-overrides.php.
 *
 * Run after LanguageTranslationSeeder (see DatabaseSeeder order).
 */
class LanguageLocalizationSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = LanguageTranslationSeeder::collectTranslationKeys();
        $packDir = config_path('demo-localization');

        foreach (Language::query()->where('is_active', true)->get() as $language) {
            $code = $language->code;
            if ($code === 'en') {
                foreach ($defaults as $key => $defaultEn) {
                    $language->translations()->updateOrCreate(
                        ['key' => $key],
                        ['value' => $defaultEn]
                    );
                }

                continue;
            }

            $path = $packDir.'/'.$code.'-overrides.php';
            $overrides = (is_file($path) && is_readable($path)) ? require $path : [];
            if (! is_array($overrides)) {
                $overrides = [];
            }

            foreach ($defaults as $key => $defaultEn) {
                $value = $overrides[$key] ?? $defaultEn;
                $language->translations()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }
    }
}
