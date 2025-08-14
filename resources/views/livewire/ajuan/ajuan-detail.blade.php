<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\StatusAjuan;
use Livewire\Attributes\{Layout, Title};
use App\Enums\InputType;

new #[Layout('components.layouts.app')] #[Title('detail pengajuan')] class extends Component
{

    #[Locked]
    public $ajuan = null;

    public $audit = [];
    public $histories = null;
    public $realisasiTanggal = null;
    public $realisasiSelisih = null;
    public $statusPengajuan = null;
    public $confirmedStatusPengajuan = null;
    public $reasonAjuan = null;
    public $statusAjuanOptions = [];
    public $selectedVendor;
    public $hpsNego = 0;
    public bool $showHpsNego = false;
    public $uploadedFile;
    public $tanggalRealisasi;
    public $textInputTambahan;
    public $vendors = [];
    public $reasonData = [];

    protected ?StatusAjuan $cachedSelectedStatus = null;
    protected $listeners = ['refreshStatusData' => 'refreshStatusData'];

    public function mount(string $ajuan = null)
    {
        $ajuan = Ajuan::findOrFail(request()->route('ajuan'));
        $ajuan->load(['status_ajuan', 'unit', 'reason_pengajuans.status_ajuan', 'reason_pengajuans.users']);
        $this->ajuan = $ajuan;
        $this->loadData();
        $this->loadReasonData();
        $this->confirmedStatusPengajuan = $ajuan->status_ajuans_id;
        $this->statusPengajuan = $this->confirmedStatusPengajuan;
        $this->statusAjuanOptions = $this->getStatusAjuanOptions();
        $this->vendors = [];
    }

    public function getUrutanStatusProperty(): bool
    {
        return $this->ajuan->status_ajuan->urutan_ajuan == 0;
    }

    public function updatedHpsNego($value)
    {
        $cleaned = preg_replace('/\D/', '', $value);
        $this->hpsNego = (int) $cleaned;
    }

    public function goBack(): void
    {
        $this->redirect('/ajuan', navigate: true);
    }

    public function updatedStatusPengajuan($value)
    {
        if (!$value) {
            $this->vendors = [];
            $this->showHpsNego = false;
            $this->cachedSelectedStatus = null;
            $this->reset(['uploadedFile', 'tanggalRealisasi', 'textInputTambahan', 'selectedVendor']);
            return;
        }
        $this->cachedSelectedStatus = StatusAjuan::find($value);
        if ($this->cachedSelectedStatus && $this->cachedSelectedStatus->input_type === InputType::SELECT_INPUT) {
            $this->vendors = \App\Models\Admin\Vendor::all();
        } else {
            $this->vendors = [];
        }
        $this->showHpsNego = true;
        $this->reset(['uploadedFile', 'tanggalRealisasi', 'textInputTambahan', 'selectedVendor']);
    }

    protected function loadData()
    {
        $this->audit = $this->ajuan->audits()->with('user')->get();
        $this->histories = $this->ajuan->statusHistories()->orderByDesc('created_at')->withPivot('realisasi')->first();
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
        } else {
            $this->realisasiTanggal = null;
            $this->realisasiSelisih = null;
        }
    }

    protected function loadReasonData()
    {
        $this->reasonData = $this->ajuan->reason_pengajuans->sortByDesc('created_at');
    }

    public function getAllStatusesProperty()
    {
        return StatusAjuan::orderBy('urutan_ajuan', 'asc')->get();
    }

    public function getPassedStatusIdsProperty()
    {
        return collect($this->audit)->map(function ($a) {
            $values = is_array($a->new_values) ? $a->new_values : json_decode($a->new_values, true);
            return $values['status_ajuans_id'] ?? null;
        })->filter()->unique()->values();
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
            ->filter(fn ($a) => $a['status_id'])
            ->sortByDesc('created_at')
            ->groupBy('status_id');
    }

    public function getStatusViewModelsProperty()
    {
        $allStatuses = $this->allStatuses;
        $passedStatusIds = $this->passedStatusIds;
        $lastStatusId = (int) $this->confirmedStatusPengajuan;
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
        $lastStatusId = $this->ajuan->status_ajuans_id;
        $total = max($allStatuses->count() - 1, 1);
        $lastIndex = $allStatuses->search(fn ($s) => $s->id == $lastStatusId);
        $progressPercent = ($lastIndex / $total) * 100;
        return [
            'total' => $total,
            'progressPercent' => $progressPercent,
        ];
    }

    public function refreshStatusData()
    {
        $this->loadData();
        $this->ajuan->refresh();
        $this->confirmedStatusPengajuan = $this->ajuan->status_ajuans_id;
        $this->statusPengajuan = $this->confirmedStatusPengajuan;
    }

    public function getStatusAjuanOptions()
    {
        if (!$this->ajuan->status_ajuan) {
            return collect([]);
        }
        return StatusAjuan::where('urutan_ajuan', '<=', $this->ajuan->status_ajuan->urutan_ajuan + 1)
            ->orderBy('urutan_ajuan')
            ->get();
    }

    public function updateStatus(): void
    {
        $data = [
            'status_ajuans_id' => $this->statusPengajuan,
        ];
        if ($this->selectedVendor !== null) {
            $data['vendor_id'] = $this->selectedVendor;
        }
        if ($this->hpsNego !== 0) {
            $data['hps_nego'] = $this->hpsNego;
        }
        $update = $this->ajuan->update($data);
        $storeReason = new \App\Models\Admin\ReasonAjuan();
        $storeReason->ajuan_id = $this->ajuan->id;
        $storeReason->status_ajuan_id = $this->statusPengajuan;
        $storeReason->created_by = auth()->id();
        $storeReason->reason_keterangan_ajuan = $this->reasonAjuan;
        $storeReason->save();
        if ($update && $storeReason) {
            $this->reset('reasonAjuan');
            $this->loadData();
            $this->ajuan->refresh();
            $this->loadReasonData();
            $this->confirmedStatusPengajuan = $this->ajuan->status_ajuans_id;
            $this->statusPengajuan = $this->confirmedStatusPengajuan;
            $this->statusAjuanOptions = $this->getStatusAjuanOptions();
            $this->dispatch('updated-status');
        }
    }

    public function getSelectedInputTypeProperty()
    {
        if ($this->cachedSelectedStatus && $this->cachedSelectedStatus->id === $this->statusPengajuan) {
            return $this->cachedSelectedStatus->input_type;
        }
        $this->cachedSelectedStatus = StatusAjuan::find($this->statusPengajuan);
        return $this->cachedSelectedStatus?->input_type;
    }
};

?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Detail Ajuan') }}
        </h2>
    </x-slot>
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-md sm:rounded-lg">
                <!-- Header -->
                <div class="px-4 py-5 sm:flex sm:items-center sm:justify-between">
                    <x-section-header title="Detail Ajuan">
                        Berikut adalah ajuan yang tersedia dengan detail informasi lengkap.
                    </x-section-header>
                    <button class="mt-4 sm:mt-0 px-4 py-2 bg-blue-100 text-sm text-blue-700 rounded-md hover:bg-blue-200 transition" wire:click="goBack">
                        @svg('heroicon-s-arrow-left', 'w-5 h-5 inline-flex mx-2')
                    </button>
                </div>
                <!-- Timeline Summary -->
                <div class="px-4 py-5">
                    <x-ajuan.timeline-summary :produk-ajuan="$this->ajuan->produk_ajuan" :produk="$this->ajuan" :histories="$histories" :realisasiTanggal="$realisasiTanggal" :realisasiSelisih="$realisasiSelisih" />
                </div>
                @if(!$this->urutanStatus)
                <!-- Timeline Progress -->
                <div class="px-4 py-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-6">Progress Status ({{ round($this->timelineData['progressPercent']) }}%)</h3>
                    <div class="overflow-x-auto max-h-48 overflow-y-auto">
                        <div class="relative min-w-[640px] w-max">
                            <!-- Garis latar -->
                            <div class="absolute top-4 left-0 w-full h-1 bg-gray-200 rounded-full"></div>
                            <!-- Garis aktif -->
                            <div class="absolute top-4 left-0 h-1 bg-green-500 rounded-full transition-all duration-700 ease-in-out" style="width: {{ $this->timelineData['progressPercent'] }}%;">
                            </div>
                            <!-- Status Items -->
                            <div class="flex justify-between relative z-10 mt-6 space-x-4 sm:space-x-6 px-2 sm:px-4">
                                @foreach ($this->statusViewModels as $status)
                                @if(!$loop->first)
                                <x-timeline-status :status="$status" />
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if (auth()->user()->hasRole('pengadaan'))
                <!-- Form Konfirmasi Status -->
                <div class="px-4 py-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Perbarui Status Ajuan</h3>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Kolom 1 - Select Status -->
                            <div>
                                <x-input-label for="statusPengajuan" :value="__('Pilih Status')" />
                                <x-select-input class="mt-1 block w-full" id="statusPengajuan" wire:model.live="statusPengajuan">
                                    <option value="">-- Pilih Status --</option>
                                    @foreach ($statusAjuanOptions as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->nama_status_ajuan }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error class="mt-1.5 text-sm" :messages="$errors->get('statusPengajuan')" />
                            </div>

                            <!-- Kolom 2 & 3 - Reason Textarea -->
                            <div class="sm:col-span-2 row-span-2">
                                <x-input-label for="reasonAjuan" :value="__('Alasan / Catatan')" />
                                <x-textarea class="mt-1 block w-full" id="reasonAjuan" wire:model="reasonAjuan" rows="4" />
                                <x-input-error class="mt-1.5 text-sm" :messages="$errors->get('reasonAjuan')" />
                            </div>

                            <!-- Kolom 1 (di bawah status) - Kondisional Input -->
                            @if ($this->selectedInputType === InputType::SELECT_INPUT)
                            <div>
                                <x-input-label for="vendor" :value="__('Pilih Vendor')" />
                                <x-select-input class="mt-1 block w-full" id="vendor" wire:model="selectedVendor">
                                    <option value="">-- Pilih Vendor --</option>
                                    @foreach ($vendors ?? [] as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            @elseif ($this->selectedInputType === InputType::FILE_INPUT)
                            <div>
                                <x-input-label for="uploadedFile" :value="__('Unggah Dokumen')" />
                                <input class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="file" wire:model="uploadedFile" />
                            </div>
                            @elseif ($this->selectedInputType === InputType::DATE_PICKER)
                            <div>
                                <x-input-label for="tanggalRealisasi" :value="__('Tanggal Realisasi')" />
                                <x-text-input class="mt-1 block w-full" id="tanggalRealisasi" type="date" wire:model="tanggalRealisasi" />
                            </div>
                            @elseif ($this->selectedInputType === InputType::TEXT_INPUT)
                            <div>
                                <x-input-label for="textInputTambahan" :value="__('Input Tambahan')" />
                                <x-text-input class="mt-1 block w-full" id="textInputTambahan" type="text" wire:model="textInputTambahan" />
                            </div>
                            @endif

                            @if ($this->showHpsNego && $this->selectedInputType != null)
                            <div>
                                <x-input-label for="hpsNego" :value="__('HPS Nego')" />
                                <x-money-input class="mt-1 block w-full" id="hpsNego" wire:model.lazy="hpsNego" autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('hpsNego')" />
                            </div>
                            @endif
                        </div>

                        <!-- Tombol -->
                        <div>
                            <x-primary-button class="h-10 px-6 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500" type="button" wire:click="updateStatus">
                                {{ __('Konfirmasi Status') }}
                            </x-primary-button>

                            <!-- Notifikasi -->
                            <x-action-message class="mt-3 text-sm text-green-600" on="updated-status">
                                {{ __('Status diperbarui.') }}
                            </x-action-message>
                        </div>
                    </form>

                </div>
                @endif

                @if (count($this->reasonData) > 0)
                <!-- Timeline Reason Status -->
                <div class="px-4 py-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Timeline </h3>
                    <div class="max-h-48 overflow-y-auto pr-2 space-y-3">
                        @foreach ($this->reasonData as $reasonLog)
                        <x-ajuan.reason-timeline>
                            <x-ajuan.reason-timeline-item :icon="view('components.icons.user')" :description="$reasonLog->reason_keterangan_ajuan" :status="$reasonLog->status_ajuan->nama_status_ajuan" :dateText="$reasonLog->updated_at" :createdBy="$reasonLog->users->name" />
                        </x-ajuan.reason-timeline>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>
