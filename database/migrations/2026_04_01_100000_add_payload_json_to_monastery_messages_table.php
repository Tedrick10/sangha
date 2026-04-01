<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monastery_messages', function (Blueprint $table) {
            $table->json('payload_json')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('monastery_messages', function (Blueprint $table) {
            $table->dropColumn('payload_json');
        });
    }
};

