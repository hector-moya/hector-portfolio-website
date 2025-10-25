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
        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->string('filename');
            $table->string('original_filename');
            $table->string('disk')->default('public');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->string('folder')->default('/');
            $table->json('meta')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
