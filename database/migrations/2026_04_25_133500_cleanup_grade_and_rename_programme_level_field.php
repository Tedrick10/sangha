<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            $programmeTypes = [
                'programme_primary',
                'programme_intermediate',
                'programme_level_1',
                'programme_level_2',
                'programme_level_3',
            ];

            DB::table('custom_fields')
                ->whereIn('entity_type', $programmeTypes)
                ->where('slug', 'level_information')
                ->update([
                    'name' => 'Level',
                    'placeholder' => 'Enter level',
                ]);

            $gradeFieldId = DB::table('custom_fields')
                ->where('entity_type', 'sangha')
                ->where('slug', 'grade')
                ->value('id');

            if ($gradeFieldId) {
                DB::table('custom_field_values')
                    ->where('custom_field_id', $gradeFieldId)
                    ->delete();

                DB::table('custom_fields')
                    ->where('id', $gradeFieldId)
                    ->delete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('custom_fields')
            ->whereIn('entity_type', [
                'programme_primary',
                'programme_intermediate',
                'programme_level_1',
                'programme_level_2',
                'programme_level_3',
            ])
            ->where('slug', 'level_information')
            ->update([
                'name' => 'Level information',
                'placeholder' => 'Enter level information',
            ]);
    }
};
