<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\StatusAjuan;

new class extends Component {
    public $audit = [];
    public $ajuan;
    public ?string $produk_ajuan = null;
    public $histories = null;
    public $realisasiTanggal = null;
    public $realisasiSelisih = null;

    protected $listeners = ['selectPengajuanHorizontal' => 'showDataHorizontal'];

    public function showDataHorizontal($id)
    {
        $this->showHorizontal = true;
        $this->ajuan = Ajuan::with(['audits.user', 'statusHistories'])->findOrFail($id);
        $this->produk_ajuan = $this->ajuan->produk_ajuan ?? '-';
        $this->audit = $this->ajuan->audits;
        $this->histories = $this->ajuan->statusHistories->sortByDesc('created_at')->first();

        if ($this->histories && $this->histories->pivot?->realisasi) {
            $realisasiDate = carbon($this->histories->pivot->realisasi)->startOfDay();
            $today = now()->startOfDay();

            $this->realisasiTanggal = $realisasiDate->translatedFormat('d F Y');
            $selisih = $realisasiDate->diffInDays($today, false);

            $this->realisasiSelisih = match (true) {
                $selisih === 0 => 'hari ini',
                $selisih > 0 => "$selisih hari yang lalu dari",
                default => abs($selisih) . ' hari lagi ke',
            };
        }
    }

    public function getAllStatusesProperty()
    {
        return StatusAjuan::orderBy('urutan_ajuan', 'asc')->get();
    }

    public function getPassedStatusIdsProperty()
    {
        return collect($this->audit)
            ->map(fn($a) => is_array($a->new_values) ? $a->new_values['status_ajuans_id'] ?? null : json_decode($a->new_values, true)['status_ajuans_id'] ?? null)
            ->filter() // ->unique()
            ->values();
    }

    public function getLastStatusIdProperty()
    {
        return $this->passedStatusIds->last();
    }

    public function getGroupedAuditProperty()
    {
        return collect($this->audit)
            ->map(function ($a) {
                $values = is_array($a->new_values) ? $a->new_values : json_decode($a->new_values, true);
                return [
                    'status_id' => $values['status_ajuans_id'] ?? null,
                    'created_at' => $a->created_at,
                    'user_name' => optional($a->user)->name ?? '-',
                ];
            })
            ->filter(fn($a) => $a['status_id'])
            ->groupBy('status_id');
    }

    public function getStatusViewModelsProperty()
    {
        $allStatuses = $this->allStatuses;
        $passedStatusIds = $this->passedStatusIds;
        $lastStatusId = $this->lastStatusId;
        $groupedAudit = $this->groupedAudit;

        return $allStatuses->map(function ($status) use ($passedStatusIds, $lastStatusId, $groupedAudit) {
            $isPassed = $passedStatusIds->contains($status->id);
            $isCurrent = $status->id == $lastStatusId;

            // Warna dot: default abu, biru jika sudah lewat, hijau jika saat ini
            $circleColor = match (true) {
                $isCurrent => 'bg-green-600',
                $isPassed => 'bg-blue-600',
                default => 'bg-gray-400',
            };

            return [
                'id' => $status->id,
                'name' => $status->nama_status_ajuan,
                'is_passed' => $isPassed,
                'is_current' => $isCurrent,
                'color' => $circleColor,
                'audits' => $groupedAudit->get($status->id, []),
            ];
        });
    }

    public function getTimelineDataProperty()
    {
        $allStatuses = $this->allStatuses;
        $lastStatusId = $this->lastStatusId;

        $total = max($allStatuses->count() - 1, 1);
        $lastIndex = $allStatuses->search(fn($s) => $s->id == $lastStatusId);
        $progressPercent = ($lastIndex / $total) * 100;

        return [
            'total' => $total,
            'progressPercent' => $progressPercent,
        ];
    }
};
?>

<section>
    <div class="w-full mb-6">
        @if ($histories && $realisasiTanggal && $realisasiSelisih)
            <h3 class="text-lg font-semibold mb-2">Estimasi realisasi menuju delivery</h3>
            <p>{{ $realisasiSelisih . ' ' . $realisasiTanggal }}</p>
        @endif
    </div>

    <div class="w-full bg-white py-10 px-4 sm:px-6 lg:px-8">
        <div class="overflow-x-auto">
            <div class="relative min-w-[640px] max-w-full">

                <!-- Progress line background -->
                <div class="absolute top-2 left-0 w-full h-1 bg-gray-300 rounded"></div>

                <!-- Progress line active -->
                <div class="absolute top-2 left-0 h-1 bg-green-500 rounded transition-all duration-700 ease-in-out animate-pulse"
                    style="width: {{ $this->timelineData['progressPercent'] }}%;">
                </div>

                <!-- Timeline items -->
                <div class="flex justify-between w-full relative z-10">
                    @foreach ($this->statusViewModels as $status)
                        <x-timeline-status :status="$status" />
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
