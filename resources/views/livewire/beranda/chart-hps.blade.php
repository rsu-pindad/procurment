<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\{StatusAjuan, Unit};
use Livewire\Attributes\On;
use App\Exports\HpsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component
{
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
        $grouped = $this->filteredAjuans->groupBy(fn ($item) => $item->jenis_ajuan ?? 'Tanpa Jenis');
        $allJenis = $grouped->keys()->toArray();
        if (!in_array('Tanpa Jenis', $allJenis)) {
            $allJenis[] = 'Tanpa Jenis';
        }
        $hpsData = $grouped->map(fn ($group) => $group->sum('hps'))->toArray();
        $hpsNegoData = $grouped->map(fn ($group) => $group->sum('hps_nego'))->toArray();
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
        return response()->streamDownload(fn () => print $pdf->stream(), $name);
    }

    #[On('exportHpsChartPdf')]
    public function exportHpsChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.hps-chart-image', ['image' => $image])->setPaper('a4', 'landscape');
        $name = 'chart-hps_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn () => print $pdf->stream(), $name);
    }
};
?>
<div class="p-4 sm:p-8 bg-white shadow rounded-md">
    <div class="mb-4">
        <x-section-header title="HPS">
            pengajuan berdasarkan hps
        </x-section-header>
    </div>
    <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-4 pb-2 border-b-2 border-slate-200">
        <x-secondary-button class="w-full sm:w-auto border-green-600 hover:bg-green-50 hover:text-green-600" wire:click="exportDataExcel">
            @svg('heroicon-o-document-arrow-down', 'size-5 mr-2 text-green-600')XLSX
        </x-secondary-button>
        <x-secondary-button class="w-full sm:w-auto border-red-600 hover:bg-red-50 hover:text-red-600" wire:click="exportDataPdf">
            @svg('heroicon-o-document-arrow-down', 'size-5 mr-2 text-red-600')PDF
        </x-secondary-button>
        <x-secondary-button class="w-full sm:w-auto border-yellow-600 hover:bg-yellow-50 hover:text-yellow-600" id="export-chart-btn">
            @svg('heroicon-o-document-chart-bar', 'size-5 mr-2 text-yellow-600')Chart
        </x-secondary-button>
    </div>
    <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-1" for="unit-filter">Filter Unit:</label>
        <x-select-input class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" id="selectedUnitId" name="selectedUnitId" wire:model.live="selectedUnitId">
            <option value="">semua</option>
            @foreach ($unitOptions as $u)
            <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
            @endforeach
        </x-select-input>
    </div>
    <div class="w-full overflow-auto border-slate-200 border p-3 rounded-md">
        <canvas class="w-full max-w-full" id="hpsChart" wire:ignore></canvas>
    </div>
</div>

@pushOnce('customScripts')
<!-- Chart Area -->
<script type="application/json" id="hpsChartLabels">
    @json($this->hpsChartData['labels'])
</script>

<script type="application/json" id="hpsChartData">
    @json($this->hpsChartData['data'])
</script>
@endpushOnce
