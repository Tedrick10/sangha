<?php

namespace Database\Seeders;

use App\Models\CustomField;
use Illuminate\Database\Seeder;

/**
 * Safe for production / cPanel: seeds active languages and all UI translation rows,
 * including Myanmar (Burmese) strings from config/demo-localization/my-overrides.php.
 *
 * Run after migrations. Does not create demo users, monasteries, or exams.
 *
 * Local နဲ့တူအောင် စာမေးပွဲ / ကျောင်းတိုက် / admin အကောင့်တွေ ပါဝင်စေချင်ရင်
 * `php artisan app:seed-demo-dataset` (သို့) `php artisan db:seed --force` ကို တစ်ကြိမ် run ပါ။
 */
class DeployLocalizationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            LanguageTranslationSeeder::class,
            LanguageLocalizationSeeder::class,
        ]);

        CustomField::syncBuiltInFieldDefinitions();
    }
}
