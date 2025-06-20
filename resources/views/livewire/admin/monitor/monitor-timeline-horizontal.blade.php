<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\StatusAjuan;
use Illuminate\Support\Arr;

new class extends Component {
    public $audit = [];
    public ?string $produk_ajuan = null;

    protected $listeners = ['selectPengajuan' => 'showData'];

    public function showData($id)
    {
        $ajuan = Ajuan::findOrFail($id);
        $this->produk_ajuan = $ajuan->produk_ajuan ?? '-';
        $this->audit = $ajuan->audits()->with('user')->get();
    }

    public function getAllStatusesProperty()
    {
        return StatusAjuan::orderBy('urutan_ajuan', 'asc')->get();
    }

    public function getPassedStatusIdsProperty()
    {
        return collect($this->audit)
            ->map(function ($a) {
                $values = is_array($a->new_values) ? $a->new_values : json_decode($a->new_values, true);
                return $values['status_ajuans_id'] ?? null;
            })
            ->filter()
            ->unique()
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
        return $this->allStatuses->map(function ($status) {
            $isPassed = $this->passedStatusIds->contains($status->id);
            $isCurrent = $status->id === $this->lastStatusId;

            $circleColor = match (true) {
                $isCurrent => 'bg-green-500',
                $isPassed => 'bg-blue-500',
                default => 'bg-gray-300',
            };

            return [
                'id' => $status->id,
                'name' => $status->nama_status_ajuan,
                'is_passed' => $isPassed,
                'is_current' => $isCurrent,
                'color' => $circleColor,
                'audits' => $this->groupedAudit->get($status->id, []),
            ];
        });
    }

    public function getTimelineDataProperty()
    {
        $total = max(count($this->allStatuses) - 1, 1);
        $lastIndex = $this->allStatuses->search(fn($s) => $s->id === $this->lastStatusId);
        $progressPercent = ($lastIndex / $total) * 100;

        return [
            'total' => $total,
            'progressPercent' => $progressPercent,
        ];
    }
}; ?>

<section>
    <div class="flex flex-col items-center justify-center w-full bg-white py-10">
        <div class="w-full overflow-x-auto">
            <div class="relative min-w-[640px] max-w-none px-4">

                {{-- Progress line --}}
                <div class="absolute top-2 left-0 w-full h-1 bg-gray-200 rounded"></div>
                <div class="absolute top-2 left-0 h-1 rounded bg-blue-500 transition-all duration-700 ease-in-out"
                    style="width: {{ $this->timelineData['progressPercent'] }}%;">
                </div>

                {{-- Timeline items --}}
                <div class="flex justify-between w-full pt-0">
                    @foreach ($this->statusViewModels as $status)
                        <x-timeline-status :status="$status" />
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
