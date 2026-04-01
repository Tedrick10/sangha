<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('password')->nullable()->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
};
