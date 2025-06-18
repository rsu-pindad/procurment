<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Notifications\StatusPengadaanNotification;

new class extends Component {
    public ?int $ajuanId = null;
    public ?Ajuan $ajuan = null;
    public ?string $notif = null;
    public bool $showModal = false;

    protected $listeners = ['show-ajuan-detail' => 'showAjuanDetail'];

    public function showAjuanDetail(string $notificationId, int $ajuanId): void
    {
        $this->ajuan = Ajuan::with(['unit', 'status_ajuan'])->findOrFail($ajuanId);
        $this->notif = $notificationId;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reset('showModal', 'ajuanId', 'ajuan');
    }

    public function accPengajuan(string $notifId, int $ajuanId): void
    {
        $ajuan = Ajuan::findOrFail($ajuanId);

        $ajuan->update([
            'status_ajuans_id' => 2,
        ]);

        auth()->user()->notifications()->where('id', $notifId)->delete();

        $ajuan->users->notify(new StatusPengadaanNotification($ajuan));
        $this->dispatch('notificationReceived');
        $this->showModal = false;
    }
}; ?>

<section>
    @if ($showModal)
        <x-modal-native name="ajuan-detail" :show="$showModal">
            <div class="p-4 space-y-4">
                <header class="relative flex items-center justify-between mb-2">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Pengajuan') }}
                    </h2>
                    <button class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Tutup modal"
                        wire:click="closeModal">
                        Tutup
                    </button>
                </header>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('berikut detail informasi pengajuan user') }}
                </p>
            </div>
            <div class="lg:col-start-3 lg:row-end-1">
                <h2 class="sr-only">Pengajuan</h2>
                <div class="rounded-lg bg-gray-50 shadow-sm ring-1 ring-gray-900/5">
                    {{-- Header HPS dan Status --}}
                    <div class="flex justify-between items-start px-6 pt-6">
                        <div>
                            <dt class="text-sm font-semibold text-gray-900">HPS</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">Rp. {{ $ajuan->hps }}</dd>
                        </div>
                        <x-pengajuan.status-label class="mt-1" :status="$ajuan->status_ajuan->nama_status_ajuan" />
                    </div>

                    {{-- Spesifikasi & Info-item --}}
                    <div class="flex flex-col lg:flex-row border-t border-gray-900/5 mt-6">
                        {{-- Kiri: Spesifikasi --}}
                        <div class="flex-1 px-6 pt-6">
                            <dt class="text-sm font-semibold text-gray-900">Spesifikasi</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">
                                {{ $ajuan->spesifikasi }}
                            </dd>
                        </div>

                        {{-- Kanan: Info Items --}}
                        <div class="flex-1 px-6 pt-6 space-y-4">
                            <x-pengajuan.info-item label="Pegawai" :value="$ajuan->users->name" :icon="'<svg class=\'h-6 w-5 text-gray-400\' aria-hidden=\'true\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z\' clip-rule=\'evenodd\' /></svg>'" />

                            <x-pengajuan.info-item label="Pengajuan Dibuat" :value="$ajuan->tanggal_ajuan" :icon="'<svg class=\'h-6 w-5 text-gray-400\' aria-hidden=\'true\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z\' clip-rule=\'evenodd\' /></svg>'" />

                            <x-pengajuan.info-item label="Jenis Ajuan" :value="$ajuan->jenis_ajuan" :icon="'<svg class=\'h-6 w-5 text-gray-400\' aria-hidden=\'true\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z\' clip-rule=\'evenodd\' /></svg>'" />
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-6 border-t border-gray-900/5 px-6 py-6 flex items-center justify-between">
                        <a class="text-sm font-semibold text-gray-900 flex items-center gap-1" href="#">
                            selengkapnya <span aria-hidden="true">&rarr;</span>
                        </a>
                        <button
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            wire:click="accPengajuan(@js($notif), @js($ajuan->id))">
                            acc
                        </button>
                    </div>
                </div>
            </div>
        </x-modal-native>
    @endif
</section>
