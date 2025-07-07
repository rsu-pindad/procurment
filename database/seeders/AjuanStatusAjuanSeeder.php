<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ajuan;
use App\Models\Admin\StatusAjuan;
use App\Models\User;

class AjuanStatusAjuanSeeder extends Seeder
{
    public function run(): void
    {
        $ajuans = Ajuan::all();
        $statusAjuans = StatusAjuan::all();
        $users = User::all();

        if ($ajuans->isEmpty() || $statusAjuans->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Data Ajuan, StatusAjuan, atau User belum tersedia. Seeder dilewati.');
            return;
        }

        foreach ($ajuans as $ajuan) {
            $status = $statusAjuans->random(); // Ambil hanya satu status acak

            DB::table('ajuan_status_ajuan')->insert([
                'ajuan_id' => $ajuan->id,
                'status_ajuan_id' => $status->id,
                'updated_by' => $users->random()->id,
                'realisasi' => now()->addDays(rand(-10, 10))->format('Y-m-d'),
                'result_realisasi' => collect(['tercapai', 'telat', 'belum'])->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeder AjuanStatusAjuan selesai.');
    }
}
