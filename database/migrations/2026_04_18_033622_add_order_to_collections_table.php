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
        Schema::table('collections', function (Blueprint $table): void {
            $table->unsignedInteger('order')->default(0)->after('id');
        });

        foreach (DB::table('collections')->oldest()->pluck('id') as $index => $id) {
            DB::table('collections')->where('id', $id)->update(['order' => $index]);
        }
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table): void {
            $table->dropColumn('order');
        });
    }
};
