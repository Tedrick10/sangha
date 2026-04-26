<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename the five canonical exam types from Burmese labels to English (IDs unchanged).
     */
    public function up(): void
    {
        $map = [
            'မူလတန်း စာမေးပွဲ' => 'Primary',
            'ဥပစာတန်း စာမေးပွဲ' => 'Intermediate',
            'ပထမဆင့် စာမေးပွဲ' => 'Level 1',
            'ဒုတိယဆင့် စာမေးပွဲ' => 'Level 2',
            'တတိယဆင့် စာမေးပွဲ' => 'Level 3',
        ];

        foreach ($map as $from => $to) {
            DB::table('exam_types')->where('name', $from)->update(['name' => $to, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $map = [
            'Primary' => 'မူလတန်း စာမေးပွဲ',
            'Intermediate' => 'ဥပစာတန်း စာမေးပွဲ',
            'Level 1' => 'ပထမဆင့် စာမေးပွဲ',
            'Level 2' => 'ဒုတိယဆင့် စာမေးပွဲ',
            'Level 3' => 'တတိယဆင့် စာမေးပွဲ',
        ];

        foreach ($map as $from => $to) {
            DB::table('exam_types')->where('name', $from)->update(['name' => $to, 'updated_at' => now()]);
        }
    }
};
