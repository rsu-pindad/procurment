<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Buat 1 user admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->syncRoles(['admin']);

        // Buat 19 user lain dengan random role selain admin
        $otherRoles = ['pengadaan', 'verifikator', 'monitoring', 'user'];

        User::factory(19)->create()->each(function ($user) use ($otherRoles) {
            $role = $otherRoles[array_rand($otherRoles)];
            $user->syncRoles([$role]);
        });
    }
}
