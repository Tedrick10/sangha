<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['name' => 'English', 'code' => 'en', 'flag' => 'GB', 'is_active' => true, 'sort_order' => 0],
            ['name' => 'Myanmar', 'code' => 'my', 'flag' => 'MM', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Thai', 'code' => 'th', 'flag' => 'TH', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Sinhala', 'code' => 'si', 'flag' => 'LK', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Pali', 'code' => 'pi', 'flag' => 'IN', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Chinese', 'code' => 'zh', 'flag' => 'CN', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Vietnamese', 'code' => 'vi', 'flag' => 'VN', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Cambodian', 'code' => 'km', 'flag' => 'KH', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Lao', 'code' => 'lo', 'flag' => 'LA', 'is_active' => true, 'sort_order' => 8],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(
                ['code' => $lang['code']],
                $lang
            );
        }
    }
}
