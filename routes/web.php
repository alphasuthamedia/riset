<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => 'halo bos ' . now()->format('h:i:s A T; l, j:F:Y.'));

Route::domain('riset.' . config('app.domain'))->group(function () {
    Route::get('/', fn () => view('riset.home'));
});