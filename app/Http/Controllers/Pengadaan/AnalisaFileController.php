<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalisaFileController extends Controller
{
    public function show($filename): StreamedResponse
    {
        $directory = 'analisa';
        // Cegah akses file di luar folder analisa
        $safePath = $directory . '/' . ltrim($filename, '/');
        if (!Storage::exists($safePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Optional: validasi agar hanya file dalam folder 'analisa' yang bisa diakses
        if (!str_starts_with($safePath, 'analisa/')) {
            abort(403, 'Akses tidak diizinkan');
        }
    }
}
