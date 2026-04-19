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
        Schema::table('blueprints', function (Blueprint $table): void {
            $table->unsignedInteger('order')->default(0)->after('id');
        });

        // Seed existing rows with sequential order based on created_at
        foreach (DB::table('blueprints')->oldest()->pluck('id') as $index => $id) {
            DB::table('blueprints')->where('id', $id)->update(['order' => $index]);
        }
    }

    public function down(): void
    {
        Schema::table('blueprints', function (Blueprint $table): void {
            $table->dropColumn('order');
        });
    }
};
