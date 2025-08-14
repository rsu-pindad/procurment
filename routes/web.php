<?php

use App\Http\Controllers\AssetController;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Pengadaan\RabFileController;
use App\Http\Controllers\Pengadaan\NodinFileController;

Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::middleware(['guest'])->group(function () {
    // Route::get('/', function () {
    //     return redirect('/login');
    // });
    Route::middleware(['redirect.role'])->get('/', function () {
        return redirect('/login');
    });
});


Route::middleware(['auth'])->group(function () {

    Route::view('dashboard', 'pages.dashboard.index')->name('dashboard');
    Route::view('profile', 'pages.profile.index')->name('profile');

    // role admin
    Route::group(['middleware' => ['role:admin']], function () {
        Route::view('unit', 'pages.unit.index')->name('unit');
        Route::view('kategori', 'pages.kategori.index')->name('kategori');
    });

    // role admin dan pengadaan
    Route::group(['middleware' => ['role:admin|pengadaan']], function () {
        Route::view('status-ajuan', 'pages.status-ajuan.index')->name('status-ajuan');
        Route::group(['prefix' => 'manajemen'], function () {
            Route::view('user', 'pages.manajemen.user')->name('manajemen.user');
            Volt::route('user/unit/{user}', 'user.manajemen-unit')->name('manajemen.user.unit');
            Volt::route('user/role/{user}', 'user.manajemen-role')->name('manajemen.user.role');
        });

        Route::view('vendors', 'pages.vendors.index')->name('vendors');
    });

    // role pengadaan dan pegawai
    Route::group(['middleware' => ['role:pengadaan|pegawai']], function () {
        Route::view('ajuan', 'pages.ajuan.index')->name('ajuan');
        Route::view('ajuan-detail/{ajuan}', 'pages.ajuan.detail')->name('ajuan.detail');
        Route::get('/rab/{filename}', [RabFileController::class, 'show'])
            ->where('filename', '.*')
            ->name('rab.show');
        Route::get('/nodin/{filename}', [NodinFileController::class, 'show'])
            ->where('filename', '.*')
            ->name('nodin.show');
        Route::get('/analisa/{filename}', [NodinFileController::class, 'show'])
            ->where('filename', '.*')
            ->name('analisa.show');
    });

    Route::group(['middleware' => ['role:monitoring']], function () {
        Route::view('monitor', 'pages.monitor.index')->name('monitor');
    });
});

Route::get('/image/{filename}', [AssetController::class, 'show']);
require __DIR__ . '/auth.php';
