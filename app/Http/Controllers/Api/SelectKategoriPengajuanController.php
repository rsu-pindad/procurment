<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\KategoriPengajuan;
use Illuminate\Support\Str;

class SelectKategoriPengajuanController extends Controller
{
    public function repoSearch(Request $request)
    {
        $query = $request->query('query', '');

        // Contoh dummy data (biasanya dari DB)
        // $repos = [
        //     ['url' => 'https://github.com/laravel/laravel', 'name' => 'Laravel Framework'],
        //     ['url' => 'https://github.com/livewire/livewire', 'name' => 'Livewire'],
        //     ['url' => 'https://github.com/tom-select/tom-select', 'name' => 'Tom Select'],
        //     ['url' => 'https://github.com/vuejs/vue', 'name' => 'Vue.js'],
        //     ['url' => 'https://github.com/reactjs/reactjs.org', 'name' => 'ReactJS'],
        // ];
        $repos = KategoriPengajuan::get();

        // Filter manual berdasarkan query (case insensitive contains)
        $filtered = $repos->filter(function ($repo) use ($query) {
            return Str::contains(strtolower($repo->name), strtolower($query));
        });

        return response()->json($filtered->values());
    }
}
