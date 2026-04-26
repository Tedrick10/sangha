<?php

use App\Models\Sangha;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->string('workflow_status', 20)->default(Sangha::STATUS_PENDING)->after('approved');
            $table->string('eligible_roll_number', 6)->nullable()->unique()->after('workflow_status');
            $table->index('workflow_status');
        });

        DB::table('sanghas')
            ->where('approved', true)
            ->update(['workflow_status' => Sangha::STATUS_APPROVED]);

        DB::table('sanghas')
            ->where('approved', false)
            ->whereNotNull('rejection_reason')
            ->update(['workflow_status' => Sangha::STATUS_REJECTED]);

        DB::table('sanghas')
            ->where('approved', false)
            ->whereNull('rejection_reason')
            ->update(['workflow_status' => Sangha::STATUS_PENDING]);
    }

    public function down(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->dropIndex(['workflow_status']);
            $table->dropUnique('sanghas_eligible_roll_number_unique');
            $table->dropColumn(['workflow_status', 'eligible_roll_number']);
        });
    }
};
