<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use App\Models\Admin\Unit;

new class extends Component {
    public string $nama_unit = '';
    public string $keterangan_unit = '';

    protected function rules()
    {
        return [
            'nama_unit' => ['required', Rule::unique('units', 'nama_unit'), 'string', 'max:255'],
            'keterangan_unit' => ['string', 'max:255'],
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

    public function storeUnit(): void
    {
        $validated = $this->validate();
        $unit = new Unit();
        $unit->fill($validated);
        $unit->save();
        $this->reset();
        $this->dispatch('profile-updated', name: $unit->nama_unit);
    }
}; ?>

<section>
    <x-primary-button x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'open-create-unit')">{{ __('Tambah Unit') }}
    </x-primary-button>
    <x-modal name="open-create-unit" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Unit') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('form unit.') }}
                </p>
            </header>
            <form class="space-y-6 space" wire:submit="storeUnit">
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
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
