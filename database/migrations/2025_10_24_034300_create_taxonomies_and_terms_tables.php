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
        Schema::create('taxonomies', function (Blueprint $table): void {
            $table->id();
            $table->string('handle')->unique();    // e.g. 'categories', 'tags'
            $table->string('name');                // Human label
            $table->boolean('hierarchical')->default(false);  // categories=true, tags=false
            $table->boolean('single_select')->default(false); // categories=true if you want only one
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('terms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('terms')->nullOnDelete(); // for hierarchical cats
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['taxonomy_id', 'slug']);
        });

        Schema::create('termables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->morphs('termable'); // termable_id, termable_type
            $table->timestamps();
            $table->unique(['term_id', 'termable_id', 'termable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('termables');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taxonomies');
    }
};
