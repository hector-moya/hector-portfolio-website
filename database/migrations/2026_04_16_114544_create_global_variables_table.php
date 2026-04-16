<?php

declare(strict_types=1);

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
        Schema::create('global_variables', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('handle');
            $table->json('value')->nullable();
            $table->foreignUlid('global_set_id')->constrained('globals')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['global_set_id', 'handle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_variables');
    }
};
