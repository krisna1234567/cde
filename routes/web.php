<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home')->name('home');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
});

require __DIR__.'/auth.php';
