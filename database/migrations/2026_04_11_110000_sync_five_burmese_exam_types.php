<?php

use App\Models\ExamType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Replace demo exam types with the five canonical Burmese programmes.
     * Exams keep rows; exam_type_id is set null on delete (nullOnDelete).
     */
    public function up(): void
    {
        $names = ExamType::CANONICAL_NAME_ORDER;

        foreach ($names as $name) {
            ExamType::updateOrCreate(
                ['name' => $name],
                ['description' => null, 'is_active' => true, 'approved' => true]
            );
        }

        ExamType::whereNotIn('name', $names)->delete();
    }

    public function down(): void
    {
        // Intentionally empty: prior demo names are not restored.
    }
};
