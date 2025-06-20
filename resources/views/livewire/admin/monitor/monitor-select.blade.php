<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;

new class extends Component {
    public $ajuan = '';
    public $selectAjuan;
    public function mount()
    {
        $this->ajuan = Ajuan::all();
    }
    public function selectChanged($id)
    {
        $this->dispatch('selectPengajuan', id: $id);
    }
}; ?>

<section>
    <div>
        <x-input-label for="unit" :value="__('Pengajuan')" />
        <x-select-input id="ajuan" name="ajuan" wire:change="selectChanged($event.target.value)">
            <option value="">{{ __('pilih pengajuan') }}</option>
            @foreach ($this->ajuan as $ajuan)
                <option value="{{ $ajuan->id }}">{{ $ajuan->produk_ajuan }}</option>
            @endforeach
        </x-select-input>
    </div>
</section>
