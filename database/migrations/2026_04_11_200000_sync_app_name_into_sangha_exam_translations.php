<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $name = (string) config('app.name');
        if ($name === '') {
            return;
        }
        DB::table('translations')->where('key', 'sangha_exam')->update(['value' => $name, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Leave values as-is; restoring old English label would overwrite intentional edits.
    }
};
