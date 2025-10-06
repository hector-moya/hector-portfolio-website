<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entry_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blueprint_element_id')->constrained()->cascadeOnDelete();
            $table->string('handle'); // field identifier from blueprint_element
            $table->text('value')->nullable(); // the actual content value
            $table->json('meta')->nullable(); // additional metadata (image dimensions, etc.)
            $table->timestamps();

            $table->index(['entry_id', 'handle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_elements');
    }
};
