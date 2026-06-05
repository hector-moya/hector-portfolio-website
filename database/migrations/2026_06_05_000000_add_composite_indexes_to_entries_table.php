<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add composite indexes matching the hot frontend listing queries:
     * "published entries ordered by published_at", both globally and scoped
     * to a blueprint/collection.
     */
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table): void {
            $table->index(['status', 'published_at'], 'entries_status_published_at_index');
            $table->index(['blueprint_id', 'status', 'published_at'], 'entries_blueprint_status_published_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table): void {
            $table->dropIndex('entries_status_published_at_index');
            $table->dropIndex('entries_blueprint_status_published_at_index');
        });
    }
};
