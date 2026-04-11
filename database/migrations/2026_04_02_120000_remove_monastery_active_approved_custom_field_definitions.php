<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Monastery Active/Approved are real columns on monasteries; duplicate built-in custom
     * fields were re-created by sync on every Custom Fields index load after delete.
     */
    public function up(): void
    {
        $ids = DB::table('custom_fields')
            ->where('entity_type', 'monastery')
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
        // No-op: definitions are no longer part of builtInFields(); avoid re-inserting stale rows.
    }
};
