<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\{StatusAjuan, Unit};
use Livewire\Attributes\On;
use App\Exports\HpsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    public $ajuans;
    public $unitOptions = [];
    public $allStatuses = [];
    public ?int $selectedUnitId = null;

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

    public function updatedSelectedUnitId()
    {
        $this->loadAjuans();
        $this->refreshCharts();
    }

    protected function refreshCharts(): void
    {
        $this->dispatch('refreshHpsChart', labels: $this->hpsChartData['labels'], data: $this->hpsChartData['data']);
    }

    public function exportDataExcel()
    {
        $data = $this->hpsChartData;
        $name = 'data-hps_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new HpsExport($data), $name);
    }

    public function exportDataPdf()
    {
        $chartData = $this->hpsChartData;
        $pdf = Pdf::loadView('exports.hps', [
            'labels' => $chartData['labels'],
            'hps' => $chartData['data']['hps'],
            'hpsNego' => $chartData['data']['hps_nego'],
        ]);
        $name = 'data-hps_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn() => print $pdf->stream(), $name);
    }

    #[On('exportHpsChartPdf')]
    public function exportHpsChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.hps-chart-image', ['image' => $image])->setPaper('a4', 'landscape');
        $name = 'chart-hps_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn() => print $pdf->stream(), $name);
    }
};
?>
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <!-- Judul -->
    <div class="mb-4">
        <x-section-header title="HPS">
            pengajuan berdasarkan hps
        </x-section-header>
    </div>

    <!-- Tombol Ekspor -->
    <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
        <x-secondary-button class="w-full sm:w-auto" wire:click="exportDataExcel">
            Export Data Excel
        </x-secondary-button>

        <x-secondary-button class="w-full sm:w-auto" wire:click="exportDataPdf">
            Export Data PDF
        </x-secondary-button>

        <x-secondary-button class="w-full sm:w-auto" id="export-chart-btn">
            Export Chart ke PDF
        </x-secondary-button>
    </div>

    <!-- Dropdown Filter Unit -->
    <div class="mb-6">
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
    <div class="w-full overflow-auto">
        <canvas class="w-full max-w-full" id="hpsChart" wire:ignore></canvas>
    </div>
</div>

@script
    <script type="module">
        let hpsChartInstance;

        document.getElementById('export-chart-btn').addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.getElementById('hpsChart');
                if (!canvas) {
                    console.warn("Chart canvas belum ditemukan.");
                    return;
                }

                const base64Image = canvas.toDataURL('image/png');

                Livewire.dispatch('exportHpsChartPdf', {
                    image: base64Image
                });
            }, 300);
        });


        async function renderHpsChart(labels, data) {
            const delay = 300;

            setTimeout(() => {
                const canvas = document.getElementById('hpsChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                const chartData = {
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
                };

                const config = {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => 'Rp ' + value.toLocaleString('id-ID')
                                }
                            }
                        },
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                color: '#333',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                };

                if (hpsChartInstance) {
                    hpsChartInstance.data.labels = labels;
                    hpsChartInstance.data.datasets[0].data = data.hps;
                    hpsChartInstance.data.datasets[1].data = data.hps_nego;
                    hpsChartInstance.update();
                } else {
                    hpsChartInstance = new Chart(ctx, config);
                }
            }, delay);
        }

        document.addEventListener('livewire:init', () => {
            renderHpsChart(@json($this->hpsChartData['labels']), @json($this->hpsChartData['data']));
        });

        Livewire.on('refreshHpsChart', ({
            labels,
            data
        }) => {
            renderHpsChart(labels, data);
        });
    </script>
@endscript
