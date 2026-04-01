<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('full_mark', 8, 2)->nullable()->after('moderation_mark');
            $table->decimal('pass_mark', 8, 2)->nullable()->after('full_mark');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['full_mark', 'pass_mark']);
        });
    }
};

