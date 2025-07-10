<?php

use Livewire\Volt\Component;
use App\Models\Admin\Unit;
use App\Models\User;
use Livewire\Attributes\Locked;

new class extends Component
{
    public ?int $selectedUnit;

    #[Locked]
    public $id;

    public $lastUserUnit;

    public function mount($user)
    {
        $this->id = $user;
        $this->lastUserUnit = User::find($user)->unit_id;
        $this->selectedUnit = $this->lastUserUnit; // <- Set default terpilih
    }

    public function units()
    {
        return Unit::all();
    }

    public function updatedSelectedUnit($value): void
    {
        $userId = (int) $this->id;
        $unit = (int) $value;
        $pegawai = User::find($userId)->update(['unit_id' => $unit]);
        $this->js("alert('Unit berhasil dipilih')");
    }

    public function goBack(): void
    {
        $this->redirect('/management/user', navigate: true);
    }
}; ?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Management User Unit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-md sm:rounded-lg">
                <!-- Header -->
                <div class="px-4 py-5 sm:flex sm:items-center sm:justify-between">
                    <x-section-header title="User Unit">
                        Berikut adalah user unit yang tersedia dengan detail informasi lengkap.
                    </x-section-header>
                    <button class="mt-4 sm:mt-0 px-4 py-2 bg-gray-100 text-sm text-gray-700 rounded-md hover:bg-gray-200 transition" wire:click="goBack">
                        ← Kembali
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    <form wire:submit="store">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                            @foreach($this->units() as $unit)
                            <div>
                                <x-toggle-switch id="unit-{{ $unit->id }}" type="radio" name="selectedUnit" value="{{ $unit->id }}" label="{{ $unit->nama_unit }}" description="{{ $unit->keterangan_unit }}" wire:model.lazy="selectedUnit" />
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
