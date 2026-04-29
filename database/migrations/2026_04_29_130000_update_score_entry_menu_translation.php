<?php

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('translations')
            ->where('key', 'menu_mandatory_score_entry')
            ->whereIn('value', ['Score entry', 'Score Entry'])
            ->update(['value' => 'Score Entry', 'updated_at' => now()]);

        $en = Language::query()->where('code', 'en')->where('is_active', true)->first();
        if ($en) {
            Translation::query()->updateOrCreate(
                ['language_id' => $en->id, 'key' => 'menu_mandatory_score_entry'],
                ['value' => 'Score Entry'],
            );
        }

        DB::table('translations')->where('key', 'mandatory_scores_nav_entry')->delete();
    }

    public function down(): void
    {
        // Intentional no-op: DB may have been cleaned of nav_entry keys.
    }
};
