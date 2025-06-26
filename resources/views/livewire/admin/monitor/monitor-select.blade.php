<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;

new class extends Component {
    public ?string $unit = null;
    public ?string $pengajuan = null;
    public $selectedUnit;
    public $pengadaans = [];
    public bool $disabledUnit = true;

    public function getListeners()
    {
        return [
            'setSelectedunit' => 'getSelectedUnit',
        ];
    }

    public function selectChanged($id)
    {
        $this->dispatch('selectPengajuan', id: $id);
        $this->dispatch('selectPengajuanHorizontal', id: $id);
    }

    public function getSelectedUnit($id)
    {
        $this->selectedUnit = $id;
        // if ($this->disabledUnit) {
        $this->disabledUnit = false;
        // } else {
        // $this->disabledUnit = true;
        // }
        // $this->dispatch('triggerDepends');
    }
}; ?>

<section class="space-y-4 sm:flex sm:space-x-6 sm:space-y-0">
    <div class="flex-1 min-w-0">
        <x-input-label for="unit" :value="__('Unit')" />
        <livewire:utility.remote-select
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            name="unit" value="id" model="App\Models\Admin\Unit" label="nama_unit" wire:model.live="unit" />
    </div>
    <div class="flex-1 min-w-0">
        <x-input-label for="pengajuan" :value="__('Pengajuan')" />
        <x-select-input
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:change="selectChanged($event.target.value)" wire:key="{{ $selectedUnit }}" :disabled="$disabledUnit">
            <option value="">
                {{ $selectedUnit ? __('silahkan pilih pengajuan') : __('pilih unit dulu') }}
            </option>
            @foreach (Ajuan::where('units_id', $this->selectedUnit)->get() as $pengadaan)
                <option value="{{ $pengadaan->id }}">{{ $pengadaan->produk_ajuan }}</option>
            @endforeach
        </x-select-input>
    </div>
</section>
