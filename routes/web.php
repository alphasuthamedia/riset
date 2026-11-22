<?php

use Illuminate\Support\Facades\Route;


Route::domain('riset.' . config('app.domain'))->group(function () {
    Route::get('/', fn () => view('riset.home'));
});