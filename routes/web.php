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
    Route::view('dashboard', 'beranda.index')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // role admin
    Route::group(['middleware' => ['role:admin']], function () {
        Route::view('unit', 'unit')->name('unit');
        Route::view('kategori', 'kategori')->name('kategori');
    });

    // role admin dan pengadaan
    Route::group(['middleware' => ['role:admin|pengadaan']], function () {
        Route::view('status-ajuan', 'status-ajuan')->name('status-ajuan');
        Route::group(['prefix' => 'manajemen'], function () {
            Route::view('user', 'manajemen.user')->name('manajemen.user');
            Volt::route('user/unit/{user}', 'user.manajemen-unit')->name('manajemen.user.unit');
            Volt::route('user/role/{user}', 'user.manajemen-role')->name('manajemen.user.role');
        });

        Route::view('vendors', 'vendors')->name('vendors');
    });

    // role pengadaan dan pegawai
    Route::group(['middleware' => ['role:pengadaan|pegawai']], function () {
        Route::view('ajuan', 'ajuan')->name('ajuan');
        Volt::route('ajuan-detail/{ajuan}', 'ajuan.ajuan-detail')->name('ajuan.detail');
        Volt::route('ajuan-edit/{ajuan}', 'ajuan.ajuan-edit')->name('ajuan.edit');
        Route::get('/rab/{filename}', [RabFileController::class, 'show'])
            ->where('filename', '.*')
            ->name('rab.show');
        Route::get('/nodin/{filename}', [NodinFileController::class, 'show'])
            ->where('filename', '.*')
            ->name('nodin.show');
        Route::get('/analisa/{filename}', [NodinFileController::class, 'show'])
            ->where('filename', '.*')
            ->name('analisa.show');
        // Route::view('monitor', 'monitor')->name('monitor');
    });

    Route::group(['middleware' => ['role:monitoring']], function(){
        Route::view('monitor','monitor')->name('monitor');
    });
});

Route::get('/image/{filename}', [AssetController::class, 'show']);
require __DIR__ . '/auth.php';
