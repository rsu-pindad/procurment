<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Ajuan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        if (Ajuan::count() === 0) {
            $this->command->warn('Tidak bisa membuat notifikasi karena data ajuan kosong.');
            return;
        }

        // Ambil semua user dengan role 'pengadaan'
        $pengadaanUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'pengadaan');
        })->get();

        if ($pengadaanUsers->isEmpty()) {
            $this->command->warn('Tidak ada user dengan role pengadaan.');
            return;
        }

        $ajuans = Ajuan::with(['unit', 'users'])->get();

        foreach ($ajuans as $ajuan) {
            foreach ($pengadaanUsers as $user) {
                $pesan = 'pengajuan baru dari unit ' . $ajuan->unit->nama_unit . PHP_EOL;
                $pesan .= 'nama atau jasa ' . $ajuan->produk_ajuan . PHP_EOL;
                $pesan .= 'oleh ' . $ajuan->users->name;

                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => \App\Notifications\PengajuanUserNotification::class,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'message' => $pesan,
                        'ajuan_id' => $ajuan->id,
                        'unit_id' => $ajuan->units_id,
                        'unit_name' => $ajuan->unit->nama_unit,
                        'created_at' => now()->toDateTimeString(),
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
