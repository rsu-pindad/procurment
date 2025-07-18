<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\{StatusAjuan, Unit};
use Livewire\Attributes\On;
use App\Exports\StatusExport;
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

    public function updatedSelectedUnitId()
    {
        $this->loadAjuans();
        $this->refreshCharts();
    }

    protected function refreshCharts(): void
    {
        $this->dispatch('refreshStatusChart', labels: $this->statusChartData['labels'], data: $this->statusChartData['data']);
    }

    public function exportDataExcel()
    {
        $data = $this->statusChartData;

        return Excel::download(new StatusExport($data), 'rekap-status_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function exportDataPdf()
    {
        $data = $this->statusChartData;
        $pdf = Pdf::loadView('exports.status', [
            'labels' => $data['labels'],
            'data' => $data['data'],
        ]);
        return response()->streamDownload(fn() => print $pdf->stream(), 'data-status_' . now()->format('Ymd_His') . '.pdf');
    }

    #[On('exportStatusChartPdf')]
    public function exportStatusChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.status-chart-image', ['image' => $image])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn() => print $pdf->stream(), 'chart-status_' . now()->format('Ymd_His') . '.pdf');
    }
};
?>
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <!-- Judul -->
    <div class="mb-4">
        <x-section-header title="Status">
            pengajuan berdasarkan status
        </x-section-header>
    </div>

    <!-- Tombol -->
    <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
        <x-secondary-button class="w-full sm:w-auto" wire:click="exportDataExcel">
            Export Data Excel
        </x-secondary-button>
        <x-secondary-button class="w-full sm:w-auto" wire:click="exportDataPdf">
            Export Data PDF
        </x-secondary-button>
        <x-secondary-button class="w-full sm:w-auto" id="export-chart-status-btn">
            Export Chart ke PDF
        </x-secondary-button>
    </div>

    <!-- Dropdown Unit -->
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
        <canvas class="w-full max-w-full" id="statusChart" wire:ignore></canvas>
    </div>
</div>

@script
    <script type="module">
        let statusChartInstance;

        document.getElementById('export-chart-status-btn').addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.getElementById('statusChart');
                if (!canvas) {
                    console.warn("Canvas statusChart belum ditemukan.");
                    return;
                }

                const base64Image = canvas.toDataURL('image/png');

                Livewire.dispatch('exportStatusChartPdf', {
                    image: base64Image
                });
            }, 300);
        });

        async function renderStatusChart(labels, data) {
            const delay = 300;

            setTimeout(() => {
                const ctx = document.getElementById('statusChart')?.getContext('2d');
                if (!ctx) return;

                const chartData = {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Ajuan per Status',
                        data: data,
                        backgroundColor: [
                            '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                        ],
                        borderWidth: 1
                    }]
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
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Distribusi Ajuan berdasarkan Status'
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'center',
                                color: '#333',
                                font: {
                                    weight: 'bold',
                                },
                                formatter: (value) => value.toLocaleString('id-ID')
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                };

                if (statusChartInstance) {
                    statusChartInstance.data.labels = labels;
                    statusChartInstance.data.datasets[0].data = data;
                    statusChartInstance.update();
                } else {
                    statusChartInstance = new Chart(ctx, config);
                }
            }, delay);
        }

        document.addEventListener('livewire:init', () => {
            renderStatusChart(@json($this->statusChartData['labels']), @json($this->statusChartData['data']));
        });

        Livewire.on('refreshStatusChart', ({
            labels,
            data
        }) => {
            renderStatusChart(labels, data);
        });
    </script>
@endscript
