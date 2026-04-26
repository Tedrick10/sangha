<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const ENTITY_TYPES = [
        'programme_primary',
        'programme_intermediate',
        'programme_level_1',
        'programme_level_2',
        'programme_level_3',
    ];

    /** @var list<string> */
    private const OPTIONS = ['Primary', 'Intermediate', 'Level 1', 'Level 2', 'Level 3'];

    public function up(): void
    {
        DB::table('custom_fields')
            ->where('slug', 'level_information')
            ->whereIn('entity_type', self::ENTITY_TYPES)
            ->update([
                'type' => 'select',
                'options' => json_encode(self::OPTIONS),
                'placeholder' => 'Select level',
            ]);
    }

    public function down(): void
    {
        DB::table('custom_fields')
            ->where('slug', 'level_information')
            ->whereIn('entity_type', self::ENTITY_TYPES)
            ->update([
                'type' => 'text',
                'options' => null,
                'placeholder' => 'Enter level',
            ]);
    }
};
