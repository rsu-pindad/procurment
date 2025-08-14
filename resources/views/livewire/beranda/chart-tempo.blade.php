<?php

use Livewire\Volt\Component;
use App\Models\Admin\Unit;
use Livewire\Attributes\On;
use App\Exports\JatuhTempoExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $unitOptions = [];
    public ?int $selectedUnitId = null;
    public string $bulanFilter = '3';
    protected ?array $cachedJatuhTempoData = null;

    public function mount()
    {
        if (auth()->user()->hasRole('pegawai')) {
            $this->unitOptions = Unit::where('id', auth()->user()->unit_id)->get();
        } else {
            $this->unitOptions = Unit::all();
        }
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
        return response()->streamDownload(fn () => print $pdf->stream(), $name);
    }

    #[On('exportJatuhTempoChartPdf')]
    public function exportJatuhTempoChartPdf($image)
    {
        $pdf = Pdf::loadView('exports.jatuh-tempo-chart-image', ['image' => $image])->setPaper('a4', 'landscape');
        $name = 'chart-tempo_' . now()->format('Ymd_His') . '.pdf';
        return response()->streamDownload(fn () => print $pdf->stream(), $name);
    }
};
?>
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <div class="mb-4">
        <x-section-header title="Tempo">
            Pengajuan berdasarkan tempo
        </x-section-header>
    </div>
    <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-4 pb-2 border-b-2 border-slate-200">
        <x-secondary-button class="w-full sm:w-auto border-green-600 hover:bg-green-50 hover:text-green-600" wire:click="exportDataExcel">
            @svg('heroicon-o-document-arrow-down', 'size-5 mr-2 text-green-600')XLSX
        </x-secondary-button>
        <x-secondary-button class="w-full sm:w-auto border-red-600 hover:bg-red-50 hover:text-red-600" wire:click="exportDataPdf">
            @svg('heroicon-o-document-arrow-down', 'size-5 mr-2 text-red-600')PDF
        </x-secondary-button>
        <x-secondary-button class="w-full sm:w-auto border-yellow-600 hover:bg-yellow-50 hover:text-yellow-600" id="export-chart-tempo-btn">
            @svg('heroicon-o-document-chart-bar', 'size-5 mr-2 text-yellow-600')Chart
        </x-secondary-button>
    </div>
    <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-1" for="bulanFilter">Filter Jatuh Tempo:</label>
        <x-select-input class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" id="bulanFilter" name="bulanFilter" wire:model.live="bulanFilter">
            <option value="3" selected>3 Bulan Lagi</option>
            <option value="2">2 Bulan Lagi</option>
            <option value="1">1 Bulan Lagi</option>
        </x-select-input>
    </div>
    <div class="w-full overflow-auto border-slate-200 border p-3 rounded-md" wire:ignore>
        <canvas class="w-full max-w-full" id="jatuhTempoChart" height="200"></canvas>
    </div>
</div>

@pushOnce('customScripts')
<script type="application/json" id="jatuhTempoChartLabels">
    @json($this->jatuhTempoChartData['labels'])
</script>

<script type="application/json" id="jatuhTempoChartData">
    @json($this->jatuhTempoChartData['data'])
</script>
@endpushOnce
