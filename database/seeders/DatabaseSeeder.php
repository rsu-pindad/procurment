<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            StatusAjuanSeeder::class,
            KategoriPengajuansSeeder::class,
            // AjuansSeeder::class,
            // AjuanStatusAjuanSeeder::class,
            // NotificationsSeeder::class,
            VendorSeeder::class,
        ]);
    }
}
