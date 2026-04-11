<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('name');
            $table->string('nrc_number', 100)->nullable()->after('father_name');
        });
    }

    public function down(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'nrc_number']);
        });
    }
};
