<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\KategoriPengajuan;

class KategoriPengajuansSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Pengadaan Barang', 'deskripsi_kategori' => 'Kategori untuk pengadaan barang'],
            ['nama_kategori' => 'Jasa Konsultansi', 'deskripsi_kategori' => 'Kategori untuk jasa konsultansi'],
            ['nama_kategori' => 'Pemeliharaan', 'deskripsi_kategori' => 'Kategori untuk pemeliharaan rutin'],
            ['nama_kategori' => 'Lain-lain', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k1', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k2', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k3', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k4', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k5', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k6', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k7', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k8', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k9', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k10', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k11', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k12', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
            ['nama_kategori' => 'k13', 'deskripsi_kategori' => 'Kategori tambahan lainnya'],
        ];

        foreach ($kategori as $item) {
            KategoriPengajuan::firstOrCreate(['nama_kategori' => $item['nama_kategori']], $item);
        }
    }
}
