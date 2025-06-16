<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Admin\Unit;
use Illuminate\Validation\Rule;

new class extends Component {
    public string $id = '';
    public string $nama_unit = '';
    public string $keterangan_unit = '';

    protected function rules()
    {
        return [
            'nama_unit' => ['required', 'lowercase', Rule::unique('units', 'nama_unit')->ignore($this->id), 'string', 'max:255'],
            'keterangan_unit' => ['string', 'lowercase', 'max:255'],
        ];
    }

    protected function messages()
    {
        return [
            'nama_unit.required' => __('validation.nama_unit.required'),
            'nama_unit.max' => __('validation.nama_unit.max'),
            'keterangan_unit.max' => __('validation.keterangan_unit.max'),
        ];
    }

    #[On('hapus')]
    public function destroy($rowId): void
    {
        $unit = Unit::find($rowId);
        $this->id = $unit->id;
        $this->dispatch('open-modal', 'confirm-unit-delete');
    }

    #[On('edit')]
    public function showEditing($rowId): void
    {
        $unit = Unit::find($rowId);
        $this->id = $unit->id;
        $this->nama_unit = $unit->nama_unit;
        $this->keterangan_unit = $unit->keterangan_unit;
        $this->dispatch('open-modal', 'open-unit');
    }

    public function editUnit(): void
    {
        $validated = $this->validate();
        $unit = Unit::find($this->id)->update([
            'nama_unit' => $this->nama_unit,
            'keterangan_unit' => $this->keterangan_unit,
        ]);
        $this->reset();
        $this->dispatch('unit-edited', name: $unit);
        $this->dispatch('pg:eventRefresh-unit-table-umnmyu-table');
    }
}; ?>

<section>
    <x-modal name="open-unit" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Unit') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('form unit.') }}
                </p>
            </header>
            <form class="space-y-6 space" wire:submit="editUnit">
                <div>
                    <x-input-label for="nama_unit" :value="__('nama unit')" />
                    <x-text-input class="mt-1 block w-full" id="nama_unit" name="nama_unit" type="text"
                        wire:model="nama_unit" required autofocus autocomplete="nama_unit" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_unit')" />
                </div>
                <div>
                    <x-input-label for="keterangan_unit" :value="__('keterangan unit')" />
                    <x-text-input class="mt-1 block w-full" id="keterangan_unit" name="keterangan_unit" type="text"
                        wire:model="keterangan_unit" />
                    <x-input-error class="mt-2" :messages="$errors->get('keterangan_unit')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Edit') }}</x-primary-button>

                    <x-action-message class="me-3" on="unit-edited">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="confirm-unit-delete" :show="$errors->isNotEmpty()" focusable>
        <div class="max-w-sm mx-auto p-6">
            <div class="flex justify-start">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __('Hapus ?') }}
                </h2>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button class="px-4 py-2" x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>
                <x-danger-button class="px-4 py-2">
                    {{ __('Hapus') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</section>
