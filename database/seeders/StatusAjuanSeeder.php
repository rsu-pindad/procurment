<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\StatusAjuan;

class StatusAjuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_status_ajuan' => 'Ajuan Unit', 'urutan_ajuan' => 1],
            ['nama_status_ajuan' => 'Pengadaan Vendor', 'urutan_ajuan' => 2],
            ['nama_status_ajuan' => 'Aanwijzing', 'urutan_ajuan' => 3],
            ['nama_status_ajuan' => 'Presentasi', 'urutan_ajuan' => 4],
            ['nama_status_ajuan' => 'Demo', 'urutan_ajuan' => 5],
            ['nama_status_ajuan' => 'Mockou', 'urutan_ajuan' => 6],
            ['nama_status_ajuan' => 'Negosiasi', 'urutan_ajuan' => 7],
            ['nama_status_ajuan' => 'Penilaian', 'urutan_ajuan' => 8],
            ['nama_status_ajuan' => 'Penyusunan PKS', 'urutan_ajuan' => 9],
            ['nama_status_ajuan' => 'Pelaksanaan / Delivery', 'urutan_ajuan' => 10],
            ['nama_status_ajuan' => 'SO / Uji Fungsi', 'urutan_ajuan' => 11],
            ['nama_status_ajuan' => 'BAST', 'urutan_ajuan' => 12],
            ['nama_status_ajuan' => 'Retensi', 'urutan_ajuan' => 13],
            ['nama_status_ajuan' => 'Pembayaran', 'urutan_ajuan' => 14],
        ];

        foreach ($data as $item) {
            StatusAjuan::create($item);
        }
    }
}
