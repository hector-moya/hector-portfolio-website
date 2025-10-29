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
        Schema::create('global_sets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('handle')->unique();
            $table->string('name');
            $table->foreignUlid('blueprint_id')->nullable()->constrained('blueprints')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('global_variables', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('handle');
            $table->json('value')->nullable();
            $table->foreignUlid('global_set_id')->constrained('global_sets')->cascadeOnDelete();
            $table->unique(['handle', 'global_set_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_variables');
        Schema::dropIfExists('global_sets');
    }
};
