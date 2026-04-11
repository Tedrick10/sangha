<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove duplicate built-in "Approved" custom field rows for exam / exam_type (real columns remain on tables).
     */
    public function up(): void
    {
        $ids = DB::table('custom_fields')
            ->whereIn('entity_type', ['exam', 'exam_type'])
            ->where('slug', 'approved')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('custom_field_values')->whereIn('custom_field_id', $ids)->delete();
        DB::table('custom_fields')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        // No-op: definitions removed from CustomField::builtInFields().
    }
};
