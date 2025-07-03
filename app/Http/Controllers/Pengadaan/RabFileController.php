<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RabFileController extends Controller
{
    public function show($filename): StreamedResponse
    {
        $directory = 'rab';
        // Cegah akses file di luar folder rab
        $safePath = $directory . '/' . ltrim($filename, '/');
        if (!Storage::exists($safePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Optional: validasi agar hanya file dalam folder 'rab' yang bisa diakses
        if (!str_starts_with($safePath, 'rab/')) {
            abort(403, 'Akses tidak diizinkan');
        }

        // Menampilkan file (stream ke browser)
        return Storage::response($safePath);
    }
}
