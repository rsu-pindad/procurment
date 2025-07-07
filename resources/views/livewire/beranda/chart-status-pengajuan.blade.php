<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;

new class extends Component {
    public $selectedJenisAjuan = 'semua';
    public $jenisAjuanList = [];
    public $chartData = [];

    public function mount()
    {
        $this->jenisAjuanList = Ajuan::distinct()->pluck('jenis_ajuan')->toArray();
        $this->updateChart();
    }

    public function updatedSelectedJenisAjuan()
    {
        $this->updateChart();
    }

    public function updateChart()
    {
        $query = Ajuan::with('status_ajuan');

        if ($this->selectedJenisAjuan !== 'semua') {
            $query->where('jenis_ajuan', $this->selectedJenisAjuan);
        }

        $data = $query->get();

        $statusCounts = $data->groupBy(fn($item) => $item->status_ajuan->nama_status_ajuan ?? 'Tanpa Status')->map(fn($group) => $group->count());

        $this->chartData = [
            'labels' => array_values($statusCounts->keys()->toArray()),
            'data' => array_values($statusCounts->values()->toArray()),
        ];
    }
};
?>
<div>
    <label for="jenisAjuan">Filter Jenis Ajuan:</label>
    <select class="form-select" id="jenisAjuan" wire:model="selectedJenisAjuan">
        <option value="semua">Semua</option>
        @foreach ($jenisAjuanList as $jenis)
            <option value="{{ $jenis }}">{{ ucfirst($jenis) }}</option>
        @endforeach
    </select>

    <canvas id="chartStatusPengajuan" wire:ignore.self width="400" height="200"></canvas>
</div>

@script
    <script type="module">
        let chartInstance;

        function renderChart(chartData) {
            const ctx = document.getElementById('chartStatusPengajuan')?.getContext('2d');
            if (!ctx || !chartData?.labels?.length) return;

            if (chartInstance) chartInstance.destroy();

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Jumlah Ajuan per Status',
                        data: chartData.data,
                        backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
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
                            text: 'Distribusi Status Pengajuan'
                        }
                    }
                }
            });
        }

        Livewire.hook('message.processed', (message, component) => {
            const canvas = document.getElementById('chartStatusPengajuan');
            if (!canvas) return;

            const compId = canvas.closest('[wire\\:id]')?.getAttribute('wire:id');
            if (component.id !== compId) return;

            const chartData = component.get('chartData');
            renderChart(chartData);
        });

        document.addEventListener('livewire:load', () => {
            const canvas = document.getElementById('chartStatusPengajuan');
            if (!canvas) return;

            const compId = canvas.closest('[wire\\:id]')?.getAttribute('wire:id');
            const component = Livewire.find(compId);
            if (!component) return;

            renderChart(component.get('chartData'));
        });
    </script>
@endscript
