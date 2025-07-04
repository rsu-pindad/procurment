<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\Unit;
use App\Models\Admin\StatusAjuan;
use Livewire\Attributes\{Layout, Title};

new #[Layout('components.layouts.app')] #[Title('detail pengajuan')] class extends Component {
    public $ajuans;
    public $unitOptions = [];
    public $allStatuses = [];
    public ?int $selectedUnitId = null;

    public function mount()
    {
        $this->ajuans = Ajuan::with('status_ajuan')->get();
        $this->unitOptions = Unit::get();
        $this->allStatuses = StatusAjuan::orderBy('urutan_ajuan')->pluck('nama_status_ajuan')->toArray();
        if (!in_array('Tanpa Status', $this->allStatuses)) {
            $this->allStatuses[] = 'Tanpa Status';
        }
    }

    public function getFilteredAjuansProperty()
    {
        return $this->selectedUnitId ? $this->ajuans->where('units_id', $this->selectedUnitId) : $this->ajuans;
    }

    public function getChartDataProperty(): array
    {
        $grouped = $this->filteredAjuans->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => count($group))->toArray();

        $finalData = [];
        foreach ($this->allStatuses as $statusName) {
            $finalData[$statusName] = $grouped[$statusName] ?? 0;
        }

        return [
            'labels' => array_keys($finalData),
            'data' => array_values($finalData),
        ];
    }

    public function updatedSelectedUnitId()
    {
        $this->dispatch('refreshStatusChart', labels: $this->chartData['labels'], data: $this->chartData['data']);
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

            <!-- Chart HPS -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <x-section-header title="HPS">
                    Pengajuan berdasarkan HPS
                </x-section-header>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                    <!-- Dropdown -->
                    <div class="col-span-1">
                    </div>

                    <!-- Chart -->
                    <div class="col-span-2" wire:ignore>
                        <canvas id="ajuanChart" height="200" width="600"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Progress -->
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
                    <div class="col-span-2" wire:ignore>
                        <canvas id="statusChart" height="200" width="600"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@script
    <script type="module">
        let hpsChartInstance = null;
        let statusChartInstance = null;

        function renderHpsChart() {
            const ctx = document.getElementById('ajuanChart').getContext('2d');
            if (hpsChartInstance) {
                hpsChartInstance.destroy();
            }

            hpsChartInstance = new Chart(ctx, {
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

        }

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
            renderHpsChart();
            renderStatusChart(@json($this->chartData['labels']), @json($this->chartData['data']));
        });

        Livewire.on('refreshStatusChart', ({
            labels,
            data
        }) => {
            if (!labels || !data) {
                console.warn("Chart data missing:", labels, data);
                return;
            }
            renderStatusChart(labels, data);
        });
    </script>
@endscript
