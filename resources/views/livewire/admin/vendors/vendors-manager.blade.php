<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Admin\Vendor;
use Illuminate\Validation\Rule;

new class extends Component
{
    public string $id = '';
    public string $nama_vendor = '';

    protected function rules()
    {
        return [
            'nama_vendor' => ['required', 'string', Rule::unique('vendors', 'nama_vendor')->ignore($this->id), 'string', 'max:255','min:3'],
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

    #[On('hapus')]
    public function destroy($rowId): void
    {
        $vendor = Vendor::find($rowId);
        $this->id = $vendor->id;
        $this->dispatch('open-modal', 'confirm-vendor-delete');
    }

    #[On('edit')]
    public function showEditing($rowId): void
    {
        $vendor = Vendor::find($rowId);
        $this->id = $vendor->id;
        $this->nama_vendor = $vendor->nama_vendor;
        $this->dispatch('open-modal', 'open-vendor');
    }

    public function editVendor(): void
    {
        $validated = $this->validate();
        $vendor = Vendor::find($this->id)->update([
            'nama_vendor' => $this->nama_vendor,
        ]);
        $this->reset();
        $this->dispatch('vendor-edited', name: $vendor);
        $this->dispatch('pg:eventRefresh-vendors-table-mqwokz-table');
    }
}; ?>

<section>
    <x-modal name="open-vendor" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Vendor') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('form vendor.') }}
                </p>
            </header>
            <form class="space-y-6 space" wire:submit="editVendor">
                <div>
                    <x-input-label for="nama_vendor" :value="__('nama vendor')" />
                    <x-text-input class="mt-1 block w-full" id="nama_vendor" name="nama_vendor" type="text" wire:model="nama_vendor" required autofocus autocomplete="nama_vendor" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_vendor')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Edit') }}</x-primary-button>

                    <x-action-message class="me-3" on="vendor-edited">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="confirm-vendor-delete" :show="$errors->isNotEmpty()" focusable>
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
