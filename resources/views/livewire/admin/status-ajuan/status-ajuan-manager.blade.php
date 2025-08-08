<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Admin\StatusAjuan;
use Illuminate\Validation\Rule;

new class extends Component
{
    public string $id = '';
    public string $nama_status_ajuan = '';
    public string $urutan_ajuan = '';

    protected function rules()
    {
        return [
            'nama_status_ajuan' => ['required', Rule::unique('status_ajuans', 'nama_status_ajuan')->ignore($this->id), 'string', 'max:255'],
            'urutan_ajuan' => ['string', Rule::unique('status_ajuans', 'nama_status_ajuan')->ignore($this->id), 'max:50', 'min:0'],
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

    #[On('hapus')]
    public function destroy($rowId): void
    {
        $statusAjuan = StatusAjuan::find($rowId);
        $this->id = $statusAjuan->id;
        $this->dispatch('open-modal', 'confirm-status-ajuan-delete');
    }

    #[On('edit')]
    public function showEditing($rowId): void
    {
        $statusAjuan = StatusAjuan::find($rowId);
        $this->id = $statusAjuan->id;
        $this->nama_status_ajuan = $statusAjuan->nama_status_ajuan;
        $this->urutan_ajuan = $statusAjuan->urutan_ajuan;
        $this->dispatch('open-modal', 'open-status-ajuan');
    }

    public function editStatusAjuan(): void
    {
        $validated = $this->validate();
        $statusAjuan = StatusAjuan::find($this->id)->update([
            'nama_status_ajuan' => $this->nama_status_ajuan,
            'urutan_ajuan' => $this->urutan_ajuan,
        ]);
        $this->reset();
        $this->dispatch('status-ajuan-edited', name: $statusAjuan);
        $this->dispatch('pg:eventRefresh-status-ajuan-table-u2v8u0-table');
    }
}; ?>

<section>
    <x-modal name="open-status-ajuan" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Status Ajuan') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('form stajus ajuan.') }}
                </p>
            </header>
            <form class="space-y-6 space" wire:submit="editStatusAjuan">
                <div>
                    <x-input-label for="nama_status_ajuan" :value="__('nama status ajuan')" />
                    <x-text-input class="mt-1 block w-full" id="nama_status_ajuan" name="nama_status_ajuan" type="text" wire:model="nama_status_ajuan" required autofocus autocomplete="nama_status_ajuan" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_status_ajuan')" />
                </div>
                <div>
                    <x-input-label for="urutan_ajuan" :value="__('urutan ajuan')" />
                    <x-text-input class="mt-1 block w-full" id="urutan_ajuan" name="urutan_ajuan" type="number" wire:model="urutan_ajuan" />
                    <x-input-error class="mt-2" :messages="$errors->get('urutan_ajuan')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Edit') }}</x-primary-button>

                    <x-action-message class="me-3" on="status-ajuan-edited">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="confirm-status-ajuan-delete" :show="$errors->isNotEmpty()" focusable>
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
