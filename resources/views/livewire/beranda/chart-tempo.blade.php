<?php

use Livewire\Volt\Component;
use App\Models\Admin\Unit;
use Livewire\Attributes\On;
use App\Exports\JatuhTempoExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $unitOptions = [];
    public ?int $selectedUnitId = null;
    public string $bulanFilter = '0';
    protected ?array $cachedJatuhTempoData = null;

    public function mount()
    {
        $this->unitOptions = Unit::all();
        $this->refreshCharts();
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

        $this->cachedJatuhTempoData = [
            'labels' => array_keys($results),
            'data' => array_values($results),
        ];

        return $this->cachedJatuhTempoData;
    }

    public function updatedBulanFilter()
    {
        $this->cachedJatuhTempoData = null;
        $this->dispatch('refreshJatuhTempoChart', labels: $this->jatuhTempoChartData['labels'], data: $this->jatuhTempoChartData['data']);
    }

    protected function refreshCharts(): void
    {
        $this->dispatch('refreshJatuhTempoChart', labels: $this->jatuhTempoChartData['labels'], data: $this->jatuhTempoChartData['data']);
    }

    public function exportDataExcel()
    {
        $data = $this->jatuhTempoChartData;
        $name = 'data-tempo_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new JatuhTempoExport($data), $name);
    }

    public function exportDataPdf()
    {
        $chartData = $this->jatuhTempoChartData;
        $pdf = Pdf::loadView('exports.hps', [
            'labels' => $chartData['labels'],
            'data' => $chartData['data'],
        ]);
        $name = 'data-tempo_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn() => print $pdf->stream(), $name);
    }

    #[On('exportJatuhTempoChartPdf')]
    public function exportJatuhTempoChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.jatuh-tempo-chart-image', ['image' => $image])->setPaper('a4', 'landscape');
        $name = 'chart-tempo_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn() => print $pdf->stream(), $name);
    }
};
?>
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <!-- Judul -->
    <div class="mb-4">
        <x-section-header title="Tempo">
            Pengajuan berdasarkan tempo
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

        <x-secondary-button class="w-full sm:w-auto" id="export-chart-tempo-btn">
            Export Chart ke PDF
        </x-secondary-button>

    </div>

    <!-- Dropdown -->
    <div class="mb-6">
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

    <!-- Chart -->
    <div class="w-full overflow-auto" wire:ignore>
        <canvas class="w-full max-w-full" id="jatuhTempoChart" height="200"></canvas>
    </div>
</div>

@script
    <script type="module">
        let jatuhTempoChartInstance;

        document.getElementById('export-chart-tempo-btn').addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.getElementById('jatuhTempoChart');
                if (!canvas) return; // pastikan canvas tersedia

                const base64Image = canvas.toDataURL('image/png');

                Livewire.dispatch('exportJatuhTempoChartPdf', {
                    image: base64Image
                });
            }, 300);
        });

        async function renderJatuhTempoChart(labels, data) {
            const delay = 300;

            setTimeout(() => {
                const canvas = document.getElementById('jatuhTempoChart');
                if (!canvas) return; // jaga-jaga jika belum dirender

                const ctx = canvas.getContext('2d');

                const chartData = {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Ajuan Jatuh Tempo',
                        data: data,
                        backgroundColor: '#f59e0b',
                        borderColor: '#d97706',
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
                            datalabels: {
                                anchor: 'end',
                                align: 'center',
                                color: '#000',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: (value) => value.toLocaleString('id-ID')
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                };

                if (jatuhTempoChartInstance) {
                    jatuhTempoChartInstance.data.labels = labels;
                    jatuhTempoChartInstance.data.datasets[0].data = data;
                    jatuhTempoChartInstance.update();
                } else {
                    jatuhTempoChartInstance = new Chart(ctx, config);
                }

                // Emit event setelah chart selesai dirender
                Livewire.dispatch('jatuhTempoChartRendered');
            }, delay);
        }


        document.addEventListener('livewire:init', () => {
            renderJatuhTempoChart(@json($this->jatuhTempoChartData['labels']), @json($this->jatuhTempoChartData['data']));
        });

        Livewire.on('refreshJatuhTempoChart', ({
            labels,
            data
        }) => {
            renderJatuhTempoChart(labels, data);
        });
    </script>
@endscript
