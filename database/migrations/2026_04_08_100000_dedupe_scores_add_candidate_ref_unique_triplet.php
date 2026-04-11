<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $groups = DB::table('scores')
            ->selectRaw('sangha_id, subject_id, exam_id, MAX(id) as keep_id, COUNT(*) as cnt')
            ->groupBy('sangha_id', 'subject_id', 'exam_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($groups as $g) {
            $q = DB::table('scores')
                ->where('sangha_id', $g->sangha_id)
                ->where('subject_id', $g->subject_id);
            if ($g->exam_id === null) {
                $q->whereNull('exam_id');
            } else {
                $q->where('exam_id', $g->exam_id);
            }
            $q->where('id', '!=', $g->keep_id)->delete();
        }

        Schema::table('scores', function (Blueprint $table) {
            if (! Schema::hasColumn('scores', 'candidate_ref')) {
                $table->string('candidate_ref', 120)->nullable();
            }
        });

        try {
            Schema::table('scores', function (Blueprint $table) {
                $table->unique(['sangha_id', 'subject_id', 'exam_id'], 'scores_sangha_subject_exam_unique');
            });
        } catch (Throwable) {
            // Index already present (e.g. re-run or manual DDL).
        }
    }

    public function down(): void
    {
        try {
            Schema::table('scores', function (Blueprint $table) {
                $table->dropUnique('scores_sangha_subject_exam_unique');
            });
        } catch (Throwable) {
            //
        }

        Schema::table('scores', function (Blueprint $table) {
            if (Schema::hasColumn('scores', 'candidate_ref')) {
                $table->dropColumn('candidate_ref');
            }
        });
    }
};
