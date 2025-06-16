<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');
Route::get('/', function () {
    return redirect('/login');
});

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');
// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');
    Route::view('profile', 'profile')
        ->name('profile');
    Route::view('unit', 'unit')->name('unit');
    Route::view('ajuan', 'ajuan')->name('ajuan');
});

require __DIR__ . '/auth.php';
