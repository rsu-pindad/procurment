<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\StatusAjuan;
use Livewire\Attributes\{Layout, Title};

new #[Layout('components.layouts.app')] #[Title('detail pengajuan')] class extends Component {
    public Ajuan $ajuan;
    public $audit = [];
    public $histories = null;
    public $realisasiTanggal = null;
    public $realisasiSelisih = null;

    // Mount menerima model langsung via route model binding
    public function mount(Ajuan $ajuan)
    {
        $this->ajuan = $ajuan;
        $this->loadData();
    }

    protected function loadData()
    {
        $this->audit = $this->ajuan->audits()->with('user')->get();
        $this->histories = $this->ajuan->statusHistories()->orderByDesc('created_at')->first();

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

    public function goBack(): void
    {
        // Redirect ke halaman sebelumnya
        // $this->redirect()->back();
        // to_route('ajuan', navigate: true);
        $this->redirect('/ajuan', navigate: true);
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
        $allStatuses = $this->allStatuses;
        $passedStatusIds = $this->passedStatusIds;
        $lastStatusId = $this->lastStatusId;
        $groupedAudit = $this->groupedAudit;

        return $allStatuses->map(function ($status) use ($passedStatusIds, $lastStatusId, $groupedAudit) {
            $isPassed = $passedStatusIds->contains($status->id);
            $isCurrent = $status->id === $lastStatusId;

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
                'audits' => $groupedAudit->get($status->id, []),
            ];
        });
    }

    public function getTimelineDataProperty()
    {
        $allStatuses = $this->allStatuses;
        $lastStatusId = $this->lastStatusId;

        $total = max($allStatuses->count() - 1, 1);
        $lastIndex = $allStatuses->search(fn($s) => $s->id === $lastStatusId);
        $progressPercent = ($lastIndex / $total) * 100;

        return [
            'total' => $total,
            'progressPercent' => $progressPercent,
        ];
    }
};
?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajuan') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="sm:flex sm:items-center">
                    <x-section-header title="Detail Ajuan">
                        Berikut adalah ajuan yang tersedia dengan detail informasi terkait masing-masing ajuan.
                    </x-section-header>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <button class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300" wire:click="goBack">
                            ← Kembali
                        </button>
                    </div>
                </div>
                <div class="mt-4 flow-root">
                    <div class="w-full mb-4">
                        @if ($histories && $realisasiTanggal && $realisasiSelisih)
                            <h3 class="text-lg font-semibold mb-2">Estimasi realisasi menuju delivery</h3>
                            <p>{{ $realisasiSelisih . ' ' . $realisasiTanggal }}</p>
                        @endif
                    </div>
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-4 lg:px-6">
                            <section class="w-full bg-white py-10 px-4 sm:px-6 lg:px-8">
                                <div class="overflow-x-auto">
                                    <div class="relative min-w-[640px] max-w-full">

                                        <!-- Progress line background -->
                                        <div class="absolute top-2 left-0 w-full h-1 bg-gray-200 rounded"></div>

                                        <!-- Progress line active -->
                                        <div class="absolute top-2 left-0 h-1 bg-blue-500 rounded transition-all duration-700 ease-in-out"
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
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
