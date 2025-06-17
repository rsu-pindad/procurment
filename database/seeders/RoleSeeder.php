<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'pengadaan', 'verifikator', 'monitoring', 'pegawai'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}
