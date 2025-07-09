<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class AssetController extends Controller
{
    public function show($filename)
    {
        $path = 'public/images/' . $filename;

        if (!Storage::exists($path)) {
            abort(404);
        }

        $file = Storage::get($path);
        $mimeType = Storage::mimeType($path);

        return response($file, 200)->header('Content-Type', $mimeType);
    }
}
