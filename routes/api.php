<?php

use App\Http\Controllers\Api\V1\CollectionsController;
use App\Http\Controllers\Api\V1\EntriesController;
use App\Http\Controllers\Api\V1\GlobalsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('entries', [EntriesController::class, 'index']);
    Route::get('entries/{slug}', [EntriesController::class, 'show']);

    Route::get('collections', [CollectionsController::class, 'index']);

    Route::get('globals', [GlobalsController::class, 'index']);
    Route::get('globals/{handle}', [GlobalsController::class, 'show']);
});
