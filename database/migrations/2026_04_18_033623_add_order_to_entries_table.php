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
        Schema::table('entries', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('id');
        });

        DB::statement('UPDATE entries SET `order` = (SELECT COUNT(*) FROM entries e2 WHERE e2.created_at <= entries.created_at) - 1');
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
