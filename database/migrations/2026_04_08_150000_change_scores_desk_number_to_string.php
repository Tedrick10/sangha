<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->string('desk_number', 64)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->unsignedInteger('desk_number')->nullable()->change();
        });
    }
};
