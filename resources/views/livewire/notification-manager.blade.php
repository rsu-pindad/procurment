<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Notifications\StatusPengadaanNotification;

new class extends Component
{
    public ?int $ajuanId = null;
    public ?Ajuan $ajuan = null;
    public ?string $notif = null;
    public bool $showModal = false;

    protected $listeners = ['eventDetail' => 'showDetail'];

    public function showDetail(string $notificationId, int $ajuanId): void
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

<div>
    @if ($showModal)
    <x-modal-native name="ajuan-detail" :show="$showModal">
        <div class="p-4 space-y-4">
            <header class="relative flex items-center justify-between mb-2">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Pengajuan') }}
                </h2>
                <button class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Tutup modal" wire:click="closeModal">
                    @svg('heroicon-s-x-circle', 'w-6 h-6')
                </button>
            </header>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('berikut detail informasi pengajuan user') }}
            </p>
        </div>
        <div class="lg:col-start-3 lg:row-end-1">
            <h2 class="sr-only">Pengajuan</h2>
            <div class="rounded-lg bg-gray-50 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex justify-between items-start px-6 pt-6">
                    <div>
                        <dt class="text-sm font-semibold text-gray-900 inline-flex">@svg('heroicon-o-currency-dollar', 'w-5 h-5 mr-2') HPS</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">Rp. {{ $ajuan->hps }}</dd>
                    </div>
                    <x-pengajuan.status-label class="mt-1" :status="$ajuan->status_ajuan->nama_status_ajuan">
                        @svg('heroicon-o-building-office', 'w-5 h-5')
                    </x-pengajuan.status-label>
                </div>
                <div class="flex flex-col lg:flex-row border-t border-gray-900/5 mt-6">
                    <div class="flex-1 px-6 pt-6">
                        <dt class="text-sm font-semibold text-gray-900 inline-flex">@svg('heroicon-o-document-text', 'w-5 h-5 mr-2') Spesifikasi</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">
                            {{ $ajuan->spesifikasi }}
                        </dd>
                    </div>
                    <div class="flex-1 px-6 pt-6 space-y-4">
                        <x-pengajuan.info-item label="Pegawai" :value="$ajuan->users->name">
                            @svg('heroicon-o-user-circle', 'w-5 h-5')
                        </x-pengajuan.info-item>
                        <x-pengajuan.info-item label="Pengajuan Dibuat" :value="$ajuan->tanggal_ajuan">
                            @svg('heroicon-o-calendar-days', 'w-5 h-5')
                        </x-pengajuan.info-item>
                        <x-pengajuan.info-item label="Jenis Ajuan" :value="$ajuan->jenis_ajuan">
                            @svg('heroicon-o-tag', 'w-5 h-5')
                        </x-pengajuan.info-item>
                    </div>
                </div>
                <div class="mt-6 border-t border-gray-900/5 px-6 py-6 flex items-center justify-between">
                    <a class="text-base font-semibold flex items-center gap-2" href="{{ route('ajuan.detail', ['ajuan' => $ajuan->id])}}">
                        Detail<span class="sr-only">
                    </a>
                    @if(auth()->user()->hasRole('pengadaan'))
                    <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500" wire:click="accPengajuan(@js($notif), @js($ajuan->id))">
                        ACC
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </x-modal-native>
    @endif
</div>
