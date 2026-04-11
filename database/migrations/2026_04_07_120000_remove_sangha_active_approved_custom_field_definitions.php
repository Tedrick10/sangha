<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sangha `is_active` and `approved` are columns on `sanghas`; duplicate built-in
     * custom field rows were redundant and could not be permanently deleted while
     * sync re-inserted them from the old builtInFields() list.
     */
    public function up(): void
    {
        $ids = DB::table('custom_fields')
            ->where('entity_type', 'sangha')
            ->whereIn('slug', ['is_active', 'approved'])
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('custom_field_values')->whereIn('custom_field_id', $ids)->delete();
        DB::table('custom_fields')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        // No-op: definitions are no longer part of builtInFields().
    }
};
