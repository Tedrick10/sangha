<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->unsignedInteger('desk_number')->nullable()->after('exam_id');
            $table->index(['exam_id', 'desk_number']);
        });
    }

    public function down(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->dropIndex(['exam_id', 'desk_number']);
            $table->dropColumn('desk_number');
        });
    }
};
