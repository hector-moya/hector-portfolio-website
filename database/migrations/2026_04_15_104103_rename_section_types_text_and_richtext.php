<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('fields')->where('type', 'text')->update(['type' => 'text_block']);
        DB::table('fields')->where('type', 'richtext')->update(['type' => 'richtext_block']);
    }

    public function down(): void
    {
        DB::table('fields')->where('type', 'text_block')->update(['type' => 'text']);
        DB::table('fields')->where('type', 'richtext_block')->update(['type' => 'richtext']);
    }
};
