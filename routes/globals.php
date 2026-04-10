<?php

declare(strict_types=1);

use App\Livewire\Globals\Create as GlobalsCreate;
use App\Livewire\Globals\Edit as GlobalsEdit;
use App\Livewire\Globals\Index as GlobalsIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('globals')->group(function (): void {
    Route::get('/', GlobalsIndex::class)->name('admin.globals.index');
    Route::get('/create', GlobalsCreate::class)->name('admin.globals.create');
    Route::get('/{globalSet}/edit', GlobalsEdit::class)->name('admin.globals.edit');
});
