<?php

use App\Http\Controllers\AssetController;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Pengadaan\RabFileController;
use App\Http\Controllers\Pengadaan\NodinFileController;

Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    // Volt::route('dashboard', 'beranda.index')->name('dashboard');
    Route::view('dashboard', 'beranda.indexs')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    Route::group(['middleware' => ['role:admin']], function () {
        Route::view('unit', 'unit')->name('unit');
        Route::view('kategori', 'kategori')->name('kategori');
    });

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
    });

    Route::group(['middleware' => 'role:pengadaan|admin'], function () {
        Route::view('status-ajuan', 'status-ajuan')->name('status-ajuan');
    });

    Route::group(['middleware' => ['role:pengadaan|pegawai']], function () {
        Route::view('monitor', 'monitor')->name('monitor');
    });

    Route::group(['prefix' => 'management'], function () {
        Route::view('user', 'management.user')->name('management.user');
        Volt::route('user/unit/{user}', 'user.management-unit')->name('management.user.unit');
    });
});

Route::get('/image/{filename}', [AssetController::class, 'show']);

require __DIR__ . '/auth.php';
