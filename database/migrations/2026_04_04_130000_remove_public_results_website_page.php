<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('websites')->where('slug', 'results')->delete();
    }

    public function down(): void
    {
        // Intentionally empty: removed page content is not restored.
    }
};
