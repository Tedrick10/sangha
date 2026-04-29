<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained('exam_types')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'exam_type_id', 'subject_id'], 'teacher_scope_unique');
            $table->index(['user_id', 'exam_type_id'], 'teacher_scope_user_exam_type_idx');
        });

        $teacherPermissions = [
            'mandatory_scores.read',
            'mandatory_scores.update',
        ];
        DB::table('roles')->updateOrInsert(
            ['name' => 'Teacher'],
            [
                'permissions' => json_encode($teacherPermissions, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subject_assignments');
        DB::table('roles')->where('name', 'Teacher')->delete();
    }
};
