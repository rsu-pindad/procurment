<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\StatusAjuan;

class StatusAjuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_ajuan' => 'Draft', 'urutan_ajuan' => 1],
            ['nama_ajuan' => 'Diajukan', 'urutan_ajuan' => 2],
            ['nama_ajuan' => 'Diproses', 'urutan_ajuan' => 3],
            ['nama_ajuan' => 'Disetujui', 'urutan_ajuan' => 4],
            ['nama_ajuan' => 'Ditolak', 'urutan_ajuan' => 5],
        ];

        foreach ($data as $item) {
            StatusAjuan::create($item);
        }
    }
}
