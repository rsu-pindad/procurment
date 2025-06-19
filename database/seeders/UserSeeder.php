<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Password diambil dari .env atau di-hardcode untuk keperluan testing (dalam kondisi produksi sebaiknya diatur di .env)
        $adminPassword = env('ADMIN_PASSWORD', 'password'); // Ganti dengan password dari env
        $defaultPassword = env('DEFAULT_PASSWORD', 'password'); // Password default untuk user selain admin

        // Buat 1 user admin (selalu dibuat)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make($adminPassword), // Ganti dengan password dari env
            ]
        );
        $admin->syncRoles(['admin']);

        // Buat user pengadaan
        $pengadaan = User::firstOrCreate(
            ['email' => 'pengadaan@example.com'],
            [
                'name' => 'Pengadaan User',
                'password' => Hash::make($defaultPassword), // Password default
            ]
        );
        $pengadaan->syncRoles(['pengadaan']);

        // Buat user pegawai
        $pegawai = User::firstOrCreate(
            ['email' => 'pegawai@example.com'],
            [
                'name' => 'Pegawai User',
                'password' => Hash::make($defaultPassword), // Password default
            ]
        );
        $pegawai->syncRoles(['pegawai']);

        // Hanya buat user acak kalau bukan production
        if (!app()->environment('production')) {
            $otherRoles = ['pengadaan', 'verifikator', 'monitoring', 'pegawai'];

            // Buat user acak secara manual (tanpa menggunakan Factory)
            for ($i = 0; $i < 19; $i++) {
                $randomName = 'User ' . ($i + 1);
                $randomEmail = 'user' . ($i + 1) . '@example.com';
                $user = User::create([
                    'name' => $randomName,
                    'email' => $randomEmail,
                    'password' => Hash::make($defaultPassword), // Password default
                ]);
                // Assign role acak
                $role = $otherRoles[array_rand($otherRoles)];
                $user->syncRoles([$role]);
            }
        }
    }
}
