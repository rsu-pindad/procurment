<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\Unit;
use App\Models\Admin\StatusAjuan;
use Livewire\Attributes\{Layout, Title, Computed};

new #[Layout('components.layouts.app')] #[Title('detail pengajuan')] class extends Component {
    public $ajuans;
    public $unitOptions = [];
    public ?int $selectedUnitId = null;

    public function mount()
    {
        $this->ajuans = Ajuan::with('status_ajuan')->get();
        $this->unitOptions = $this->getUnitOptions();
    }

    #[Computed]
    public function getUnitOptions()
    {
        return Unit::get();
    }

    #[Computed]
    public function filteredAjuans()
    {
        return $this->selectedUnitId ? $this->ajuans->where('units_id', $this->selectedUnitId) : $this->ajuans;
    }

    #[Computed]
    public function groupedByJenisAjuan(): array
    {
        return $this->ajuan->groupBy('jenis_ajuan')->map(fn($group) => $group->sum('hps'))->toArray();
    }

    #[Computed]
    public function groupedByStatusAjuan(): array
    {
        return $this->filteredAjuans->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => count($group))->toArray();
    }

    // public function getChartDataProperty(): array
    // {
    //     return [
    //         'labels' => array_keys($this->groupedByStatusAjuan),
    //         'data' => array_values($this->groupedByStatusAjuan),
    //     ];
    // }

    // public function getChartDataProperty(): array
    // {
    //     $allStatuses = \App\Models\Admin\StatusAjuan::orderBy('urutan_ajuan')->pluck('nama_status_ajuan')->toArray();

    //     $grouped = $this->filteredAjuans->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => count($group))->toArray();

    //     // Gabungkan dengan semua status (set default 0 jika tidak ada)
    //     $chartData = [];
    //     foreach ($allStatuses as $status) {
    //         $chartData[$status] = $grouped[$status] ?? 0;
    //     }

    //     return [
    //         'labels' => array_keys($chartData),
    //         'data' => array_values($chartData),
    //     ];
    // }

    public function getChartDataProperty(): array
    {
        // Ambil semua status sesuai urutan
        $orderedStatuses = StatusAjuan::orderBy('urutan_ajuan')->pluck('nama_status_ajuan')->toArray();
        // dd($orderedStatuses);
        // dump($orderedStatuses);
        // Illuminate\Support\Facades\Log::info($orderedStatuses);

        // Tambahkan label 'Tanpa Status' kalau belum ada
        if (!in_array('Tanpa Status', $orderedStatuses)) {
            $orderedStatuses[] = 'Tanpa Status';
        }

        // Hitung jumlah per status
        $grouped = $this->filteredAjuans->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => count($group))->toArray();
        // Illuminate\Support\Facades\Log::info($grouped);
        // Susun hasil chart lengkap dengan 0 jika kosong
        $chartData = [];
        foreach ($orderedStatuses as $status) {
            $chartData[$status] = $grouped[$status] ?? 0;
        }
        // Illuminate\Support\Facades\Log::info($chartData);

        return [
            'labels' => array_keys($chartData),
            'data' => array_values($chartData),
        ];
    }

    // public function getChartData()
    // {
    //     $labels = array_keys($this->groupedByStatusAjuan);
    //     $data = array_values($this->groupedByStatusAjuan);

    //     \Log::info('Dispatching chart update:', [
    //         'labels' => $labels,
    //         'data' => $data,
    //     ]);

    //     $this->dispatch('refreshStatusChart', labels: $labels, data: $data);
    // }

    public function getChartData()
    {
        // Ambil semua status master (dijamin urut)
        $allStatuses = StatusAjuan::orderBy('urutan_ajuan')->pluck('nama_status_ajuan')->toArray();

        // Ambil data ajuan hasil filter unit
        $actualData = $this->filteredAjuans->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => count($group))->toArray();

        // Tambahkan 'Tanpa Status' jika belum ada di master
        if (!in_array('Tanpa Status', $allStatuses)) {
            $allStatuses[] = 'Tanpa Status';
        }

        // Buat array final dengan default 0
        $finalData = [];
        foreach ($allStatuses as $statusName) {
            $finalData[$statusName] = $actualData[$statusName] ?? 0;
        }

        Illuminate\Support\Facades\Log::info('Dispatching chart update:', [
            'labels' => array_keys($finalData),
            'data' => array_values($finalData),
        ]);

        $this->dispatch('refreshStatusChart', labels: array_keys($finalData), data: array_values($finalData));
    }

    public function updatedSelectedUnitId()
    {
        $this->getChartData();
    }
};
?>
<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Beranda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="sm:flex sm:items-center">
                    <x-section-header title="HPS">
                        Pengajuan berdasarkan HPS
                    </x-section-header>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <canvas id="ajuanChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <x-section-header title="Progress Pengajuan">
                    Progress pengajuan berdasarkan status
                </x-section-header>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                    <!-- Dropdown -->
                    <div class="col-span-1">
                        <label class="text-sm text-gray-600 block mb-1" for="unit-filter">Filter Unit:</label>
                        <x-select-input
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            id="selectedUnitId" name="selectedUnitId" wire:model.live="selectedUnitId">
                            <option value="">-- Pilih Unit --</option>
                            @foreach ($unitOptions as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
                            @endforeach
                        </x-select-input>
                    </div>

                    <!-- Chart -->
                    <div class="col-span-2">
                        <canvas id="statusChart" height="200" width="600"></canvas>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>

@script
    <script type="module">
        let statusChartInstance = null;

        function renderStatusChart(labels, data) {
            const ctx = document.getElementById('statusChart').getContext('2d');

            if (statusChartInstance) {
                statusChartInstance.destroy();
            }

            statusChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Ajuan per Status',
                        data: data,
                        backgroundColor: [
                            '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Ajuan berdasarkan Status'
                        }
                    }
                }
            });
        }

        document.addEventListener('livewire:navigated', () => {
            renderStatusChart(@json($this->chartData['labels']), @json($this->chartData['data']));

            const ctx = document.getElementById('ajuanChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($this->chartData['labels']),
                    datasets: [{
                        label: 'Total HPS per Jenis Ajuan',
                        data: @json($this->chartData['data']),
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
                                callback: value => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        });

        // Render ulang saat state Livewire berubah
        Livewire.on('refreshStatusChart', (...args) => {
            const [{
                labels,
                data
            }] = args;

            if (!labels || !data) {
                console.warn("Chart data missing:", args);
                return;
            }

            renderStatusChart(labels, data);
        });
    </script>
@endscript
