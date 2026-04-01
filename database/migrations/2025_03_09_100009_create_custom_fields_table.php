<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // monastery, sangha, exam, exam_type
            $table->string('name'); // display label
            $table->string('slug'); // field key for form
            $table->string('type')->default('text'); // text, textarea, number, date, select, checkbox
            $table->json('options')->nullable(); // for select: ["Option 1", "Option 2"]
            $table->boolean('required')->default(false);
            $table->string('placeholder')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['entity_type', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
