<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\StatusAjuan;

class StatusAjuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_status_ajuan' => 'Ajuan Unit', 'urutan_ajuan' => 1, 'input_type' => ''],
            ['nama_status_ajuan' => 'Pengadaan Vendor', 'urutan_ajuan' => 2, 'input_type' => ''],
            ['nama_status_ajuan' => 'Aanwijzing', 'urutan_ajuan' => 3, 'input_type' => ''],
            ['nama_status_ajuan' => 'Presentasi / Demo / Mockou', 'urutan_ajuan' => 4, 'input_type' => ''],
            ['nama_status_ajuan' => 'Negosiasi', 'urutan_ajuan' => 5, 'input_type' => ''],
            ['nama_status_ajuan' => 'Penilaian', 'urutan_ajuan' => 6, 'input_type' => 'select_input'],
            ['nama_status_ajuan' => 'Penyusunan PKS', 'urutan_ajuan' => 7, 'input_type' => ''],
            ['nama_status_ajuan' => 'Pelaksanaan / Delivery', 'urutan_ajuan' => 8, 'input_type' => ''],
            ['nama_status_ajuan' => 'SO / Uji Fungsi', 'urutan_ajuan' => 9, 'input_type' => ''],
            ['nama_status_ajuan' => 'BAST', 'urutan_ajuan' => 10, 'input_type' => ''],
            ['nama_status_ajuan' => 'Retensi', 'urutan_ajuan' => 11, 'input_type' => ''],
            ['nama_status_ajuan' => 'Pembayaran', 'urutan_ajuan' => 12, 'input_type' => ''],
        ];

        foreach ($data as $item) {
            StatusAjuan::create($item);
        }
    }
}
