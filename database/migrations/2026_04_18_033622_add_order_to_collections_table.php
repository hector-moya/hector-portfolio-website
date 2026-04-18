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
        Schema::table('collections', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('id');
        });

        DB::statement('UPDATE collections SET `order` = (SELECT COUNT(*) FROM collections c2 WHERE c2.created_at <= collections.created_at) - 1');
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
