<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monastery_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monastery_id')->constrained()->cascadeOnDelete();
            $table->string('sender_type', 20); // monastery|admin
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monastery_messages');
    }
};

