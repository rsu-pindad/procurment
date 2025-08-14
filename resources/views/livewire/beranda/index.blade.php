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
                <!-- Judul -->
                <div class="mb-4">
                    <x-section-header title="HPS">
                        Pengajuan berdasarkan HPS
                    </x-section-header>
                </div>

                <!-- Tombol-tombol -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                    <x-primary-button class="w-full sm:w-auto" wire:click="exportExcel">
                        📥 Export Excel
                    </x-primary-button>

                    <x-secondary-button class="w-full sm:w-auto" wire:click="exportPdf">
                        🧾 Export PDF
                    </x-secondary-button>

                    <x-secondary-button class="w-full sm:w-auto" id="export-chart-btn">
                        🖼️ Export Chart ke PDF
                    </x-secondary-button>
                </div>

                <!-- Chart -->
                <div class="w-full overflow-auto" wire:ignore>
                    <canvas class="w-full max-w-full" id="ajuanChart" height="200"></canvas>
                </div>
            </div>

            <!-- Chart Progress -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <!-- Judul -->
                <div class="mb-4">
                    <x-section-header title="Progress Pengajuan">
                        Progress pengajuan berdasarkan status
                    </x-section-header>
                </div>

                <!-- Tombol-tombol -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                    <x-secondary-button class="w-full sm:w-auto" id="export-chart-progress-btn">
                        🖼️ Export Chart Progress ke PDF
                    </x-secondary-button>

                    <x-secondary-button class="w-full sm:w-auto" wire:click="exportStatusExcel">
                        📊 Export Status Excel
                    </x-secondary-button>
                </div>

                <!-- Dropdown -->
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
                <div class="w-full overflow-auto" wire:ignore>
                    <canvas class="w-full max-w-full" id="statusChart" height="200"></canvas>
                </div>
            </div>

            <!-- Chart Tempo -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <!-- Judul -->
                <div class="mb-4">
                    <x-section-header title="Tempo Pengajuan">
                        Pengajuan berdasarkan tempo
                    </x-section-header>
                </div>

                <!-- Tombol-tombol -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                    <x-secondary-button class="w-full sm:w-auto" id="export-chart-progress-btn">
                        🖼️ Export Chart Progress ke PDF
                    </x-secondary-button>

                    <x-secondary-button class="w-full sm:w-auto" wire:click="exportJatuhTempoExcel">
                        ⏰ Export Jatuh Tempo Excel
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

        </div>
    </div>
</section>
