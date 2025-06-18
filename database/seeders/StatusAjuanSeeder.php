<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\StatusAjuan;

class StatusAjuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_status_ajuan' => 'Draft', 'urutan_ajuan' => 1],
            ['nama_status_ajuan' => 'Diajukan', 'urutan_ajuan' => 2],
            ['nama_status_ajuan' => 'Diproses', 'urutan_ajuan' => 3],
            ['nama_status_ajuan' => 'Disetujui', 'urutan_ajuan' => 4],
            ['nama_status_ajuan' => 'Ditolak', 'urutan_ajuan' => 5],
        ];

        foreach ($data as $item) {
            StatusAjuan::create($item);
        }
    }
}
