<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monastery_form_requests', function (Blueprint $table) {
            $table->foreignId('exam_type_id')->nullable()->after('monastery_id')->constrained('exam_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('monastery_form_requests', function (Blueprint $table) {
            $table->dropForeign(['exam_type_id']);
        });
    }
};
