<?php

namespace Database\Seeders;

use App\Models\Admin\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_unit' => 'A', 'keterangan_unit' => 1],
            ['nama_unit' => 'B', 'keterangan_unit' => 2],
            ['nama_unit' => 'C', 'keterangan_unit' => 3],
            ['nama_unit' => 'D', 'keterangan_unit' => 4],
            ['nama_unit' => 'E', 'keterangan_unit' => 5],
        ];

        foreach ($data as $item) {
            Unit::firstOrCreate(
                ['nama_unit' => $item['nama_unit']],
                ['keterangan_unit' => $item['keterangan_unit']]
            );
        }
    }
}
