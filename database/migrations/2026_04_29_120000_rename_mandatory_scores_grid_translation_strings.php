<?php

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const KEYS = ['mandatory_scores_nav_grid', 'mandatory_scores_grid_title'];

    private const NEW_LABEL = 'Exam Mark Entry';

    public function up(): void
    {
        DB::table('translations')
            ->whereIn('key', self::KEYS)
            ->where(function ($q): void {
                $q->where('value', 'Exam desk grid')
                    ->orWhere('value', 'Exam desk score grid')
                    ->orWhere('value', 'Exam Desk Score Grid')
                    ->orWhere('value', 'like', 'Exam desk score grid%');
            })
            ->update(['value' => self::NEW_LABEL, 'updated_at' => now()]);

        $en = Language::query()->where('code', 'en')->where('is_active', true)->first();
        if ($en) {
            foreach (self::KEYS as $key) {
                Translation::query()->updateOrCreate(
                    ['language_id' => $en->id, 'key' => $key],
                    ['value' => self::NEW_LABEL],
                );
            }
        }
    }

    public function down(): void
    {
        // Intentionally empty: restoring legacy English risks overwriting intentional edits.
    }
};
