<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ajuan;
use App\Models\Admin\KategoriPengajuan;
use App\Models\Admin\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\JenisAjuan;

class AjuansSeeder extends Seeder
{
    public function run(): void
    {
        // Cek data referensi
        if (
            Unit::count() === 0 ||
            User::count() === 0 ||
            KategoriPengajuan::count() === 0
        ) {
            $this->command->warn('Tidak bisa menjalankan AjuansSeeder karena data referensi kosong.');
            return;
        }

        $kategoriIds = KategoriPengajuan::pluck('id')->toArray();
        $unitIds = Unit::pluck('id')->toArray();

        // Ambil user dengan role "pegawai"
        $pegawaiUserIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'pegawai');
        })->pluck('id')->toArray();

        if (empty($pegawaiUserIds)) {
            $this->command->warn('Tidak ada user dengan role "pegawai", seeder AjuansSeeder dilewati.');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $ajuan = Ajuan::create([
                'units_id' => collect($unitIds)->random(),
                'tanggal_ajuan' => now(),
                'produk_ajuan' => 'Produk Contoh ' . $i,
                'hps' => rand(5000000, 15000000),
                'spesifikasi' => 'Spesifikasi produk contoh ke-' . $i,
                'file_rab' => null,
                'file_nota_dinas' => null,
                'file_analisa_kajian' => null,
                // 'jenis_ajuan' => 'Barang',
                'jenis_ajuan' => collect([JenisAjuan::RKAP, JenisAjuan::NONRKAP])->random()->value, // ✅ gunakan value dari enum
                'tanggal_update_terakhir' => now(),
                'status_ajuans_id' => 1, // << tetap statis
                'users_id' => collect($pegawaiUserIds)->random(),
            ]);

            // Hubungkan ke 1–3 kategori acak
            $kategoriUntukAjuan = collect($kategoriIds)->random(rand(1, 3))->toArray();
            $ajuan->kategori_pengajuans()->attach($kategoriUntukAjuan);
        }
    }
}
