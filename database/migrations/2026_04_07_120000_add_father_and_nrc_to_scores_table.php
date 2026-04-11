<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            if (! Schema::hasColumn('scores', 'father_name')) {
                $table->string('father_name', 255)->nullable();
            }
            if (! Schema::hasColumn('scores', 'nrc_number')) {
                $table->string('nrc_number', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $cols = array_values(array_filter([
                Schema::hasColumn('scores', 'father_name') ? 'father_name' : null,
                Schema::hasColumn('scores', 'nrc_number') ? 'nrc_number' : null,
            ]));
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
