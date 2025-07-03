<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use Livewire\Attributes\{Layout, Title};

new #[Layout('components.layouts.app')] #[Title('detail pengajuan')] class extends Component {
    public array $labels = [];
    public array $data = [];

    public function mount()
    {
        // Ambil data
        $ajuans = Ajuan::get();

        // Group berdasarkan jenis_ajuan, dan jumlahkan hps
        $grouped = $ajuans->groupBy('jenis_ajuan')->map(fn($g) => $g->sum('hps'));

        // Simpan ke properti untuk dibaca di Blade
        $this->labels = $grouped->keys()->toArray(); // ['rkap', 'nonrkap']
        $this->data = $grouped->values()->toArray(); // [total_hps_rkap, total_hps_nonrkap]
    }
}; ?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Beranda') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-md sm:rounded-lg">
                <canvas id="ajuanChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

</section>

@script
    <script type="module">
        document.addEventListener('livewire:navigated', function() {
            const ctx = document.getElementById('ajuanChart').getContext('2d');

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Total HPS per Jenis Ajuan',
                        data: {!! json_encode($data) !!},
                        backgroundColor: ['#3b82f6', '#10b981'],
                        borderColor: ['#2563eb', '#059669'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endscript
