<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['web', 'auth']]);

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
    Route::view('monitor', 'monitor')->name('monitor');

    // Route::get('/notifikasi', \App\Livewire\Notifikasi::class)->name('notifikasi');
});

require __DIR__ . '/auth.php';
