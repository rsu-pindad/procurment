<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\Unit;
use App\Models\Admin\StatusAjuan;
use Livewire\Attributes\{Layout, Title, On};
use App\Exports\HpsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] #[Title('detail pengajuan')] class extends Component {
    public $ajuans;
    public $unitOptions = [];
    public $allStatuses = [];
    public ?int $selectedUnitId = null;
    public string $bulanFilter = '0';
    protected ?array $cachedJatuhTempoData = null;

    public function mount()
    {
        $this->unitOptions = Unit::all();
        $this->allStatuses = StatusAjuan::orderBy('urutan_ajuan')->pluck('nama_status_ajuan')->toArray();

        if (!in_array('Tanpa Status', $this->allStatuses)) {
            $this->allStatuses[] = 'Tanpa Status';
        }
        $this->loadAjuans();
        $this->refreshCharts();
    }

    protected function loadAjuans(): void
    {
        $query = Ajuan::with('status_ajuan');
        if ($this->selectedUnitId) {
            $query->where('units_id', $this->selectedUnitId);
        }

        $this->ajuans = $query->get();
    }

    public function getFilteredAjuansProperty()
    {
        // return $this->selectedUnitId ? $this->ajuans->where('units_id', $this->selectedUnitId) : $this->ajuans;
        return $this->ajuans;
    }

    public function getHpsChartDataProperty(): array
    {
        $grouped = $this->filteredAjuans->groupBy(fn($item) => $item->jenis_ajuan ?? 'Tanpa Jenis');
        $allJenis = $grouped->keys()->toArray();

        if (!in_array('Tanpa Jenis', $allJenis)) {
            $allJenis[] = 'Tanpa Jenis';
        }

        $hpsData = $grouped->map(fn($group) => $group->sum('hps'))->toArray();
        $hpsNegoData = $grouped->map(fn($group) => $group->sum('hps_nego'))->toArray();

        $finalHps = [];
        $finalHpsNego = [];

        foreach ($allJenis as $jenis) {
            $finalHps[$jenis] = $hpsData[$jenis] ?? 0;
            $finalHpsNego[$jenis] = $hpsNegoData[$jenis] ?? 0;
        }

        return [
            'labels' => array_keys($finalHps),
            'data' => [
                'hps' => array_values($finalHps),
                'hps_nego' => array_values($finalHpsNego),
            ],
        ];
    }

    public function getStatusChartDataProperty(): array
    {
        $grouped = $this->filteredAjuans->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => $group->count())->toArray();

        $finalData = [];
        foreach ($this->allStatuses as $statusName) {
            $finalData[$statusName] = $grouped[$statusName] ?? 0;
        }

        return [
            'labels' => array_keys($finalData),
            'data' => array_values($finalData),
        ];
    }

    public function getJatuhTempoChartDataProperty(): array
    {
        if ($this->cachedJatuhTempoData !== null) {
            return $this->cachedJatuhTempoData;
        }

        $bulan = (int) $this->bulanFilter;
        $startDate = now();
        $endDate = now()->addMonths($bulan)->endOfMonth();

        $results = DB::table('ajuan_status_ajuan')
            ->join('ajuans', 'ajuan_status_ajuan.ajuan_id', '=', 'ajuans.id')
            ->join('status_ajuans', 'ajuan_status_ajuan.status_ajuan_id', '=', 'status_ajuans.id')
            ->whereBetween('ajuan_status_ajuan.realisasi', [$startDate, $endDate])
            ->selectRaw('status_ajuans.nama_status_ajuan as status, COUNT(*) as total')
            ->groupBy('status_ajuans.nama_status_ajuan')
            ->pluck('total', 'status')
            ->toArray();

        $labels = array_keys($results);
        $data = array_values($results);

        $this->cachedJatuhTempoData = [
            'labels' => $labels,
            'data' => $data,
        ];

        return $this->cachedJatuhTempoData;
    }

    public function updatedSelectedUnitId()
    {
        $this->loadAjuans();
        $this->refreshCharts();
    }

    public function updatedBulanFilter()
    {
        $this->cachedJatuhTempoData = null;
        $this->dispatch('refreshJatuhTempoChart', labels: $this->jatuhTempoChartData['labels'], data: $this->jatuhTempoChartData['data']);
    }

    protected function refreshCharts(): void
    {
        $this->dispatch('refreshStatusChart', labels: $this->statusChartData['labels'], data: $this->statusChartData['data']);
        $this->dispatch('refreshHpsChart', labels: $this->hpsChartData['labels'], data: $this->hpsChartData['data']);
        $this->dispatch('refreshJatuhTempoChart', labels: $this->jatuhTempoChartData['labels'], data: $this->jatuhTempoChartData['data']);
    }

    public function exportExcel()
    {
        $data = $this->hpsChartData;
        $name = 'rekap-hps_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new HpsExport($data), $name);
    }

    public function exportPdf()
    {
        $chartData = $this->hpsChartData;
        $pdf = Pdf::loadView('exports.hps', [
            'labels' => $chartData['labels'],
            'hps' => $chartData['data']['hps'],
            'hpsNego' => $chartData['data']['hps_nego'],
        ]);
        $name = 'rekap-hps_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn() => print $pdf->stream(), $name);
    }

    #[On('exportHpsChartPdf')]
    public function exportHpsChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.hps-chart-image', ['image' => $image])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn() => print $pdf->stream(), 'chart-hps.pdf');
    }

    #[On('exportStatusChartPdf')]
    public function exportStatusChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.status-chart-image', ['image' => $image])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn() => print $pdf->stream(), 'chart-status.pdf');
    }

    public function exportStatusExcel()
    {
        $data = $this->statusChartData;
        return Excel::download(new \App\Exports\StatusExport($data), 'rekap-status_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function exportJatuhTempoExcel()
    {
        $data = $this->jatuhTempoChartData;
        return Excel::download(new \App\Exports\JatuhTempoExport($data), 'rekap-jatuh-tempo_' . now()->format('Ymd_His') . '.xlsx');
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

                <x-primary-button wire:click="exportExcel">
                    📥 Export Excel
                </x-primary-button>

                <x-secondary-button wire:click="exportPdf">
                    🧾 Export PDF
                </x-secondary-button>

                <x-secondary-button id="export-chart-btn">
                    🖼️ Export Chart ke PDF
                </x-secondary-button>

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

                <x-secondary-button id="export-chart-progress-btn">
                    🖼️ Export Chart Progress ke PDF
                </x-secondary-button>

                <x-secondary-button wire:click="exportStatusExcel">
                    📊 Export Status Excel
                </x-secondary-button>

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

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <x-section-header title="Progress Pengajuan">
                    Progress pengajuan berdasarkan status
                </x-section-header>

                <x-secondary-button id="export-chart-progress-btn">
                    🖼️ Export Chart Progress ke PDF
                </x-secondary-button>

                <x-secondary-button wire:click="exportJatuhTempoExcel">
                    ⏰ Export Jatuh Tempo Excel
                </x-secondary-button>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                    <div class="col-span-1">
                        <label class="text-sm text-gray-600 block mb-1" for="bulanFilter">Filter Jatuh Tempo:</label>
                        <x-select-input
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            id="bulanFilter" name="bulanFilter" wire:model.live="bulanFilter">
                            <option value="">-- Pilih Jatuh Tempo --</option>
                            <option value="3">3 Bulan Lagi</option>
                            <option value="2">2 Bulan Lagi</option>
                            <option value="1">1 Bulan Lagi</option>
                        </x-select-input>
                    </div>
                    <div class="col-span-2" wire:ignore>
                        <canvas id="jatuhTempoChart" height="200" width="600"></canvas>
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
        let jatuhTempoChartInstance = null;

        // window.downloadHpsChartAsPdf = function() {
        //     const canvas = document.getElementById('ajuanChart');
        //     const base64Image = canvas.toDataURL('image/png');

        //     Livewire.dispatch('exportHpsChartPdf', {
        //         image: base64Image
        //     });
        // };

        document.getElementById('export-chart-btn').addEventListener('click', () => {
            const canvas = document.getElementById('ajuanChart');
            const base64Image = canvas.toDataURL('image/png');

            Livewire.dispatch('exportHpsChartPdf', {
                image: base64Image
            });
        });

        document.getElementById('export-chart-progress-btn').addEventListener('click', () => {
            const canvas = document.getElementById('statusChart');
            const base64Image = canvas.toDataURL('image/png');

            Livewire.dispatch('exportStatusChartPdf', {
                image: base64Image
            });
        });

        function renderHpsChart(labels, data) {
            if (hpsChartInstance) {
                hpsChartInstance.clear();
                hpsChartInstance.destroy();
            }
            const ctx = document.getElementById('ajuanChart').getContext('2d');

            hpsChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total HPS',
                            data: data.hps,
                            backgroundColor: '#3b82f6',
                            borderColor: '#2563eb',
                            borderWidth: 1
                        },
                        {
                            label: 'Total HPS Nego',
                            data: data.hps_nego,
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1
                        }
                    ]
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
            if (statusChartInstance) {
                statusChartInstance.clear();
                statusChartInstance.destroy();
            }
            const ctx = document.getElementById('statusChart').getContext('2d');

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

        function renderJatuhTempoChart(labels, data) {
            if (jatuhTempoChartInstance) {
                jatuhTempoChartInstance.clear();
                jatuhTempoChartInstance.destroy();
            }
            const ctx = document.getElementById('jatuhTempoChart').getContext('2d');

            jatuhTempoChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Ajuan Jatuh Tempo',
                        data: data,
                        backgroundColor: '#f59e0b',
                        borderColor: '#d97706',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        document.addEventListener('livewire:init', () => {
            renderHpsChart(@json($this->hpsChartData['labels']), @json($this->hpsChartData['data']));
            renderStatusChart(@json($this->statusChartData['labels']), @json($this->statusChartData['data']));
            renderJatuhTempoChart(@json($this->jatuhTempoChartData['labels']), @json($this->jatuhTempoChartData['data']));
        });

        Livewire.on('refreshStatusChart', ({
            labels,
            data
        }) => {
            renderStatusChart(labels, data);
        });

        Livewire.on('refreshHpsChart', ({
            labels,
            data
        }) => {
            renderHpsChart(labels, data);
        });


        Livewire.on('refreshJatuhTempoChart', ({
            labels,
            data
        }) => {
            renderJatuhTempoChart(labels, data);
        });
    </script>
@endscript
