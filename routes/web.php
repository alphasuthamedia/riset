<?php

use App\Http\Controllers\RisetController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => 'halo bos ' . now()->format('h:i:s A T; l, j:F:Y.'));

Route::domain('riset.' . config('app.domain'))->name('riset.')->group(function () {
    Route::get('/', [RisetController::class, 'index'])
        ->name('index');
    Route::get('/{slug}', [RisetController::class, 'riset'])
        ->where('slug', '[a-z0-9-]+')
        ->name('show');
});