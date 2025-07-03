<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NodinFileController extends Controller
{
    public function show($filename): StreamedResponse
    {
        $directory = 'nodin';
        // Cegah akses file di luar folder nodin
        $safePath = $directory . '/' . ltrim($filename, '/');
        if (!Storage::exists($safePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Optional: validasi agar hanya file dalam folder 'nodin' yang bisa diakses
        if (!str_starts_with($safePath, 'nodin/')) {
            abort(403, 'Akses tidak diizinkan');
        }

        // Menampilkan file (stream ke browser)
        return Storage::response($safePath);
    }
}
