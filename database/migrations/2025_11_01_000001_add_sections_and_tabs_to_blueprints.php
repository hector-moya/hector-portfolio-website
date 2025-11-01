<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blueprint_tabs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('blueprint_id');
            $table->string('name');
            $table->string('handle');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('blueprint_id')
                ->references('id')
                ->on('blueprints')
                ->onDelete('cascade');
        });

        Schema::create('blueprint_sections', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('blueprint_id');
            $table->ulid('tab_id')->nullable();
            $table->string('name');
            $table->string('handle');
            $table->text('instructions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('blueprint_id')
                ->references('id')
                ->on('blueprints')
                ->onDelete('cascade');

            $table->foreign('tab_id')
                ->references('id')
                ->on('blueprint_tabs')
                ->onDelete('cascade');
        });

        // Update blueprint_elements to reference sections
        Schema::table('blueprint_elements', function (Blueprint $table): void {
            $table->ulid('section_id')->nullable()->after('blueprint_id');

            $table->foreign('section_id')
                ->references('id')
                ->on('blueprint_sections')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('blueprint_elements', function (Blueprint $table): void {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });

        Schema::dropIfExists('blueprint_sections');
        Schema::dropIfExists('blueprint_tabs');
    }
};
