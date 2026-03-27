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
        Schema::table('assets', function (Blueprint $table): void {
            $table->text('caption')->nullable()->after('alt_text');
            $table->text('description')->nullable()->after('caption');
            $table->string('copyright')->nullable()->after('description');
            $table->json('focal_point')->nullable()->after('copyright');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table): void {
            $table->dropColumn(['caption', 'description', 'copyright', 'focal_point']);
        });
    }
};
