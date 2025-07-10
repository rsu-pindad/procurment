<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\KategoriPengajuan;

class KategoriPengajuansSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Umum', 'deskripsi_kategori' => 'kategori umum pengadaan barang'],
            ['nama_kategori' => 'Alat Kesehatan', 'deskripsi_kategori' => 'kategori alat kesehatan'],
            ['nama_kategori' => 'Konstruksi', 'deskripsi_kategori' => 'kategori konstruksi barang'],
            ['nama_kategori' => 'Jasa', 'deskripsi_kategori' => 'kategori jasa'],
            ['nama_kategori' => '', 'deskripsi_kategori' => ''],
            ['nama_kategori' => 'Investaris Kantor', 'deskripsi_kategori' => 'kategori investaris kantor'],
        ];

        foreach ($kategori as $item) {
            KategoriPengajuan::firstOrCreate(['nama_kategori' => $item['nama_kategori']], $item);
        }
    }
}
