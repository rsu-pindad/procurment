<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use App\Models\Admin\StatusAjuan;

new class extends Component
{
    public string $nama_status_ajuan = '';
    public string $urutan_ajuan = '';

    protected function rules()
    {
        return [
            'nama_status_ajuan' => ['required', Rule::unique('status_ajuans', 'nama_status_ajuan'), 'string', 'max:255'],
            'urutan_ajuan' => ['numeric', Rule::unique('status_ajuans', 'urutan_ajuan'), 'max:50', 'min:0'],
        ];
    }

    protected function messages()
    {
        return [
            'nama_status_ajuan.required' => __('validation.nama_status_ajuan.required'),
            'nama_status_ajuan.max' => __('validation.nama_status_ajuan.max'),
            'urutan_ajuan.max' => __('validation.urutan_ajuan.max'),
            'urutan_ajuan.min' => __('validation.urutan_ajuan.min'),
        ];
    }

    public function storeStatusAjuan(): void
    {
        $validated = $this->validate();
        $statusAjuan = new StatusAjuan();
        $statusAjuan->fill($validated);
        $statusAjuan->save();
        $this->reset();
        $this->dispatch('statusAjuan-stored', name: $statusAjuan->nama_status_ajuan);
        $this->dispatch('pg:eventRefresh-status-ajuan-table-u2v8u0-table');
    }
}; ?>

<section>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'open-create-status-ajuan')">{{ __('Tambah Status Ajuan') }}
    </x-primary-button>
    <x-modal name="open-create-status-ajuan" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Status Ajuan') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Masukkan informasi status ajuan pada form di bawah ini.') }}
                </p>
            </header>
            <form class="space-y-6" wire:submit="storeStatusAjuan">
                <div>
                    <x-input-label for="nama_status_ajuan" :value="__('nama status ajuan')" />
                    <x-text-input class="mt-1 block w-full" id="nama_status_ajuan" name="nama_status_ajuan" type="text" wire:model="nama_status_ajuan" required autofocus autocomplete="nama_status_ajuan" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_status_ajuan')" />
                </div>
                <div>
                    <x-input-label for="urutan_ajuan" :value="__('urutan status ajuan')" />
                    <x-text-input class="mt-1 block w-full" id="urutan_ajuan" name="urusan_ajuan" type="number" wire:model="urutan_ajuan" required autofocus autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('urutan_ajuan')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                    <x-action-message class="me-3" on="statusAjuan-stored">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
