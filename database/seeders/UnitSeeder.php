<?php

namespace Database\Seeders;

use App\Models\Admin\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_unit' => 'DIR', 'keterangan_unit' => 'direktorat'],
            ['nama_unit' => 'RSB', 'keterangan_unit' => 'rumah sakit pindad bandung'],
            ['nama_unit' => 'RST', 'keterangan_unit' => 'rumah sakit pindad turen'],
            ['nama_unit' => 'RSL', 'keterangan_unit' => 'rsl'],
            ['nama_unit' => 'KUL', 'keterangan_unit' => 'klinik utama lembang'],
        ];

        foreach ($data as $item) {
            Unit::firstOrCreate(
                ['nama_unit' => $item['nama_unit']],
                ['keterangan_unit' => $item['keterangan_unit']]
            );
        }
    }
}
