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
        Schema::create('translations', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 10);
            $table->morphs('translatable'); // This will create translatable_type and translatable_id columns
            $table->string('field');
            $table->text('value');
            $table->timestamps();

            $table->unique(['locale', 'translatable_type', 'translatable_id', 'field']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
