<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use App\Models\Admin\KategoriPengajuan;

new class extends Component {
    public string $nama_kategori = '';
    public string $deskripsi_kategori = '';

    protected function rules()
    {
        return [
            'nama_kategori' => ['required', 'lowercase', Rule::unique('kategori_pengajuans', 'nama_kategori'), 'string', 'max:255'],
            'deskripsi_kategori' => ['string', 'max:255'],
        ];
    }

    protected function messages()
    {
        return [
            'nama_kategori.required' => __('validation.nama_kategori.required'),
            'nama_kategori.max' => __('validation.nama_kategori.max'),
            'deskripsi_kategori.max' => __('validation.deskripsi_kategori.max'),
        ];
    }

    public function storeKategori(): void
    {
        $validated = $this->validate();
        $kategori = new KategoriPengajuan();
        $kategori->fill($validated);
        $kategori->save();
        $this->reset();
        $this->dispatch('kategori-stored', name: $kategori->nama_kategori);
        $this->dispatch('pg:eventRefresh-kategori-pengajuan-table-qgladb-table');
    }
}; ?>

<section>
    <x-primary-button x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'open-create-kategori')">{{ __('Tambah Kategori') }}
    </x-primary-button>
    <x-modal name="open-create-kategori" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Kategori') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Masukkan informasi kategori pada form di bawah ini.') }}
                </p>
            </header>
            <form class="space-y-6" wire:submit="storeKategori">
                <div>
                    <x-input-label for="nama_kategori" :value="__('nama kategori')" />
                    <x-text-input class="mt-1 block w-full" id="nama_kategori" name="nama_kategori" type="text"
                        wire:model="nama_kategori" required autofocus autocomplete="nama_kategori" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_kategori')" />
                </div>
                <div>
                    <x-input-label for="deskripsi_kategori" :value="__('keterangan kategori')" />
                    <x-textarea id="deskripsi_kategori" name="keterangan" wire:model="deskripsi_kategori" autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('deskripsi_kategori')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                    <x-action-message class="me-3" on="kategori-stored">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
