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
    public $statusPengajuan = null;
    public $reasonAjuan = null;
    public $statusAjuanOptions = [];
    protected $listeners = ['refreshStatusData' => 'refreshStatusData'];

    public function mount(Ajuan $ajuan)
    {
        $ajuan->load(['status_ajuan', 'unit']);
        $this->ajuan = $ajuan;
        $this->loadData();
        $this->loadReasonData(['status_ajuan', 'users']);
        $this->statusPengajuan = $this->ajuan->status_ajuans_id;
        $this->statusAjuanOptions = $this->getStatusAjuanOptions();
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

    protected function loadReasonData()
    {
        $this->reasonData = $this->ajuan->reason_pengajuans->sortByDesc('created_at');
    }

    public function goBack(): void
    {
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
        return (int) $this->passedStatusIds->last();
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
            ->sortByDesc('created_at')
            ->groupBy('status_id');
    }

    public function getStatusViewModelsProperty()
    {
        $allStatuses = $this->allStatuses;
        $passedStatusIds = $this->passedStatusIds;
        // $lastStatusId = $this->lastStatusId;
        $lastStatusId = (int) $this->statusPengajuan;
        $groupedAudit = $this->groupedAudit;

        return $allStatuses->map(function ($status) use ($passedStatusIds, $lastStatusId, $groupedAudit) {
            $isPassed = $passedStatusIds->contains($status->id);
            $isCurrent = (int) $status->id === $lastStatusId;

            $circleColor = match (true) {
                $isCurrent => 'bg-green-600',
                $isPassed => 'bg-blue-600',
                default => 'bg-gray-400',
            };

            return [
                'id' => (int) $status->id,
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
        // $lastStatusId = $this->lastStatusId;
        $lastStatusId = $this->ajuan->status_ajuans_id;

        $total = max($allStatuses->count() - 1, 1);
        $lastIndex = $allStatuses->search(fn($s) => $s->id == $lastStatusId);
        $progressPercent = ($lastIndex / $total) * 100;

        return [
            'total' => $total,
            'progressPercent' => $progressPercent,
        ];
    }

    public function refreshStatusData()
    {
        $this->loadData();
        $this->statusPengajuan = $this->lastStatusId;
    }

    public function getStatusAjuanOptions()
    {
        if (!$this->ajuan->status_ajuan) {
            return [];
        }

        return StatusAjuan::where('urutan_ajuan', '<=', $this->ajuan->status_ajuan->urutan_ajuan + 1)
            ->orderBy('urutan_ajuan')
            ->get();
    }

    public function updateStatus(): void
    {
        $update = $this->ajuan->update(['status_ajuans_id' => $this->statusPengajuan]);

        $storeReason = new \App\Models\Admin\ReasonAjuan();
        $storeReason->ajuan_id = $this->ajuan->id;
        $storeReason->status_ajuan_id = $this->statusPengajuan;
        $storeReason->created_by = auth()->id();
        $storeReason->reason_keterangan_ajuan = $this->reasonAjuan;
        $storeReason->save();

        if ($update && $storeReason) {
            $this->reset('reasonAjuan');

            $this->loadData();

            $this->ajuan->refresh(); // ⬅️ Tambahkan ini agar reason_pengajuans ikut di-refresh
            $this->loadReasonData();

            $this->statusPengajuan = $this->ajuan->status_ajuans_id;
            $this->statusAjuanOptions = $this->getStatusAjuanOptions();

            $this->dispatch('updated-status');
        }
    }
};
?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Ajuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-md sm:rounded-lg">
                <!-- Header -->
                <div class="px-4 py-5 sm:flex sm:items-center sm:justify-between">
                    <x-section-header title="Detail Ajuan">
                        Berikut adalah ajuan yang tersedia dengan detail informasi lengkap.
                    </x-section-header>
                    <button
                        class="mt-4 sm:mt-0 px-4 py-2 bg-gray-100 text-sm text-gray-700 rounded-md hover:bg-gray-200 transition"
                        wire:click="goBack">
                        ← Kembali
                    </button>
                </div>

                <!-- Timeline Summary -->
                <div class="px-4 py-5">
                    <x-ajuan.timeline-summary :produk-ajuan="$this->ajuan->produk_ajuan" :produk="$this->ajuan" :histories="$histories" :realisasiTanggal="$realisasiTanggal"
                        :realisasiSelisih="$realisasiSelisih" />
                </div>

                <!-- Timeline Progress -->
                <div class="px-4 py-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-6">Progress Status
                        ({{ round($this->timelineData['progressPercent']) }}%)</h3>
                    <div class="overflow-x-auto max-h-48 overflow-y-auto">
                        <div class="relative min-w-[640px] w-max">
                            <!-- Garis latar -->
                            <div class="absolute top-4 left-0 w-full h-1 bg-gray-200 rounded-full"></div>

                            <!-- Garis aktif -->
                            <div class="absolute top-4 left-0 h-1 bg-green-500 rounded-full transition-all duration-700 ease-in-out"
                                style="width: {{ $this->timelineData['progressPercent'] }}%;">
                            </div>

                            <!-- Status Items -->
                            <div class="flex justify-between relative z-10 mt-6 space-x-4 sm:space-x-6 px-2 sm:px-4">
                                @foreach ($this->statusViewModels as $status)
                                    <x-timeline-status :status="$status" />
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                @if (auth()->user()->hasRole('pengadaan'))
                    <!-- Form Konfirmasi Status -->
                    <div class="px-4 py-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Perbarui Status Ajuan</h3>
                        <form>
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                                <!-- Status Dropdown -->
                                <div class="w-full sm:max-w-xs">
                                    <x-input-label for="statusPengajuan" :value="__('Pilih Status')" />
                                    <x-select-input
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                        id="statusPengajuan" name="statusPengajuan" wire:model="statusPengajuan">
                                        <option value="">-- Pilih Status --</option>
                                        @foreach ($statusAjuanOptions as $sp)
                                            <option value="{{ $sp->id }}">{{ $sp->nama_status_ajuan }}</option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error class="mt-1.5 text-sm" :messages="$errors->get('statusPengajuan')" />
                                </div>

                                <!-- Reason Textarea -->
                                <div class="w-full sm:max-w-lg">
                                    <x-input-label for="reason" :value="__('Alasan / Catatan')" />
                                    <x-textarea
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                        id="reason" name="reason" wire:model="reasonAjuan" rows="3" />
                                    <x-input-error class="mt-1.5 text-sm" :messages="$errors->get('reasonAjuan')" />
                                </div>

                                <!-- Tombol -->
                                <div class="w-full sm:w-auto">
                                    <x-primary-button
                                        class="w-full sm:w-auto h-9 px-5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                                        type="button" wire:click="updateStatus">
                                        {{ __('Konfirmasi Status') }}
                                    </x-primary-button>
                                </div>
                            </div>

                            <!-- Notifikasi -->
                            <x-action-message class="mt-3 text-sm text-green-600" on="updated-status">
                                {{ __('Status diperbarui.') }}
                            </x-action-message>
                        </form>
                    </div>
                @endif

                @isset($this->reasonData)
                    <!-- Timeline Reason Status -->
                    <div class="px-4 py-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Timeline </h3>
                        <div class="max-h-48 overflow-y-auto pr-2 space-y-3">
                            @foreach ($this->reasonData as $reasonLog)
                                <x-ajuan.reason-timeline>
                                    <x-ajuan.reason-timeline-item :icon="view('components.icons.user')" :description="$reasonLog->reason_keterangan_ajuan" :status="$reasonLog->status_ajuan->nama_status_ajuan"
                                        :dateText="$reasonLog->updated_at" :createdBy="$reasonLog->users->name" />
                                </x-ajuan.reason-timeline>
                            @endforeach
                        </div>
                    </div>
                @endIsset

            </div>
        </div>
    </div>

</section>
