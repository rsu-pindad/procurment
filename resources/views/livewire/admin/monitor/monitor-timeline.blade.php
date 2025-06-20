<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\StatusAjuan;

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
        return StatusAjuan::orderBy('urutan_ajuan', 'desc')->get();
    }

    public function getPassedStatusIdsProperty()
    {
        return collect($this->audit)->map(fn($a) => is_array($a->new_values) ? $a->new_values : json_decode($a->new_values, true))->map(fn($values) => $values['status_ajuans_id'] ?? null)->filter()->unique()->values();
    }

    public function getLastStatusIdProperty()
    {
        return $this->passedStatusIds->last();
    }

    public function getStatusesWithAuditProperty()
    {
        $passedIds = $this->passedStatusIds;
        $lastId = $this->lastStatusId;

        // Group audits by status id
        $groupedAudit = collect($this->audit)
            ->map(function ($audit) {
                $values = is_array($audit->new_values) ? $audit->new_values : json_decode($audit->new_values, true);
                return [
                    'status_id' => $values['status_ajuans_id'] ?? null,
                    'created_at' => $audit->created_at,
                    'user_name' => optional($audit->user)->name ?? '-',
                ];
            })
            ->filter(fn($item) => $item['status_id'])
            ->groupBy('status_id');

        return $this->allStatuses->map(function ($status) use ($passedIds, $lastId, $groupedAudit) {
            $isPassed = $passedIds->contains($status->id);
            $isCurrent = $status->id === $lastId;

            $circleColor = match (true) {
                $isCurrent => 'bg-green-500',
                $isPassed => 'bg-blue-500',
                default => 'bg-gray-300',
            };

            $borderColor = str_replace('bg', 'border', $circleColor);

            return [
                'id' => $status->id,
                'name' => $status->nama_status_ajuan,
                'is_passed' => $isPassed,
                'is_current' => $isCurrent,
                'circle_color' => $circleColor,
                'border_color' => $borderColor,
                'audits' => $groupedAudit->get($status->id, collect()),
            ];
        });
    }
}; ?>

<section>
    @foreach ($this->statusesWithAudit as $status)
        <x-timeline-vertical-item :status="$status" :produk-ajuan="$this->produk_ajuan" />
    @endforeach

</section>
