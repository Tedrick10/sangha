<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->unsignedSmallInteger('top20_position')->nullable()->index()->after('approved');
        });
    }

    public function down(): void
    {
        Schema::table('sanghas', function (Blueprint $table) {
            $table->dropColumn('top20_position');
        });
    }
};

