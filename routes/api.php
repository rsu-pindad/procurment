<?php

use App\Http\Controllers\Api\DependRemoteSelectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RemoteSelectController;
use App\Http\Controllers\Api\SelectKategoriPengajuanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('select-kategori{query?}', [SelectKategoriPengajuanController::class, 'repoSearch']);
Route::get('remote-select', [RemoteSelectController::class, 'fetch']);
Route::get('depend-remote-select', [DependRemoteSelectController::class, 'fetch']);
