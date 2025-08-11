<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use App\Models\Admin\Vendor;

new class extends Component
{
    public string $nama_vendor = '';
    protected function rules()
    {
        return [
            'nama_vendor' => ['required', 'string', Rule::unique('vendors', 'nama_vendor'), 'string', 'max:255', 'min:3'],
        ];
    }

    protected function messages()
    {
        return [
            'nama_vendor.required' => __('validation.nama_vendor.required'),
            'nama_vendor.max' => __('validation.nama_vendor.max'),
            'nama_vendor.min' => __('validation.nama_vendor.min'),
        ];
    }

    public function storeKategori(): void
    {
        $validated = $this->validate();
        $vendor = new Vendor();
        $vendor->fill($validated);
        $vendor->save();
        $this->reset();
        $this->dispatch('vendor-stored', name: $vendor->nama_vendor);
        $this->dispatch('pg:eventRefresh-vendors-table-ffgqhz-table');
    }
}; ?>

<section>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'open-create-vendor')">{{ __('Tambah Vendor') }}
    </x-primary-button>
    <x-modal name="open-create-vendor" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Vendor') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Masukkan informasi vendor pada form di bawah ini.') }}
                </p>
            </header>
            <form class="space-y-6" wire:submit="storeKategori">
                <div>
                    <x-input-label for="nama_vendor" :value="__('nama vendor')" />
                    <x-text-input class="mt-1 block w-full" id="nama_vendor" name="nama_vendor" type="text" wire:model="nama_vendor" required autofocus autocomplete="nama_vendor" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_vendor')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                    <x-action-message class="me-3" on="vendor-stored">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
