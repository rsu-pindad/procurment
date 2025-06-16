<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Buat 1 user admin (selalu dibuat)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // Ganti dengan env('ADMIN_PASSWORD') jika perlu
            ]
        );
        $admin->syncRoles(['admin']);

        // Hanya buat user random kalau bukan production
        if (!app()->environment('production')) {
            $otherRoles = ['pengadaan', 'verifikator', 'monitoring', 'user'];

            User::factory(19)->create()->each(function ($user) use ($otherRoles) {
                $role = $otherRoles[array_rand($otherRoles)];
                $user->syncRoles([$role]);
            });
        }
    }
}
