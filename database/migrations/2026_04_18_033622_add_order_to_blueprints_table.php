<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blueprints', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('id');
        });

        // Seed existing rows with sequential order based on created_at
        DB::statement('UPDATE blueprints SET `order` = (SELECT COUNT(*) FROM blueprints b2 WHERE b2.created_at <= blueprints.created_at) - 1');
    }

    public function down(): void
    {
        Schema::table('blueprints', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
