<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use App\Models\Ajuan;
use Livewire\WithFileUploads;
use App\Enums\JenisAjuan;

new class extends Component {
    use WithFileUploads;

    public ?string $units_id = null;
    public ?string $tanggal_ajuan = null;
    public ?string $hps = '0';
    public ?string $spesifikasi = null;
    public $file_rab;
    public $file_nota_dinas;
    public $file_analisa_kajian;
    public ?string $jenis_ajuan = null;

    protected function rules()
    {
        return [
            'tanggal_ajuan' => ['required', 'date'],
            'hps' => ['required', 'numeric', 'min:10000', 'max:500000000'],
            'spesifikasi' => ['required', 'string', 'max:255'],
            'file_rab' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'file_nota_dinas' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'file_analisa_kajian' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'jenis_ajuan' => ['required'],
        ];
    }

    protected function messages()
    {
        return [
            'tanggal_ajuan.required' => __('validation.tanggal_ajuan.required'),
            'tanggal_ajuan.date' => __('validation.tanggal_ajuan.date'),
            'hps.required' => __('validation.hps.required'),
            'hps.numeric' => __('validation.hps.numeric'),
            'hps.min' => __('validation.hps.min'),
            'hps.max' => __('validation.hps.max'),
            'file_rab.required' => __('validation.file_rab.required'),
            'file_rab.file' => __('validation.file_rab.file'),
            'file_rab.mimes' => __('validation.file_rab.mimes'),
            'file_rab.max' => __('validation.file_rab.max'),
            'file_nota_dinas.required' => __('validation.file_nota_dinas.required'),
            'file_nota_dinas.file' => __('validation.file_nota_dinas.file'),
            'file_nota_dinas.mimes' => __('validation.file_nota_dinas.mimes'),
            'file_nota_dinas.max' => __('validation.file_nota_dinas.max'),
            'file_analisa_kajian.required' => __('validation.file_analisa_kajian.required'),
            'file_analisa_kajian.file' => __('validation.file_analisa_kajian.file'),
            'file_analisa_kajian.mimes' => __('validation.file_analisa_kajian.mimes'),
            'file_analisa_kajian.max' => __('validation.file_analisa_kajian.max'),
            'jenis_ajuan.required' => __('validation.jenis_ajuan.required'),
        ];
    }

    public function updatedHps($value)
    {
        // $this->hps = preg_replace('/\D/', '', $value);
        $cleaned = preg_replace('/\D/', '', $value);
        $this->hps = (int) $cleaned;
    }

    public function store(): void
    {
        $validated = $this->validate();
        try {
            $pathRab = $this->file_rab->store('rab');
            $pathNodin = $this->file_nota_dinas->store('nodin');
            $pathAnalisa = $this->file_analisa_kajian->store('analisa');

            $ajuan = new Ajuan();
            $ajuan->units_id = auth()->user()->units_id ?? 1;
            $ajuan->tanggal_ajuan = $this->tanggal_ajuan;
            $ajuan->hps = (int) $this->hps;
            $ajuan->spesifikasi = $this->spesifikasi;
            $ajuan->file_rab = $pathRab;
            $ajuan->file_nota_dinas = $pathNodin;
            $ajuan->file_analisa_kajian = $pathAnalisa;
            $ajuan->jenis_ajuan = $this->jenis_ajuan;
            $ajuan->tanggal_update_terakhir = now();
            $ajuan->status_ajuans_id = 1;
            $ajuan->users_id = auth()->id();
            $ajuan->save();

            $this->reset();
            $this->dispatch('modal-stored', name: 'pengajuan');
            // logger($ajuan);
        } catch (\Throwable $th) {
            // logger($th->getMessage());
            $this->dispatch('modal-stored', name: $th->getMessage());
        }
    }
}; ?>

<section>
    <x-primary-button x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'open-create-modal')">{{ __('Ajukan Barang') }}
    </x-primary-button>
    <x-modal name="open-create-modal" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Pengajuan Barang') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('form pengajuan barang.') }}
                </p>
            </header>
            <form wire:submit.prevent="store">
                <!-- Grid untuk input fields -->
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="tanggal_ajuan" :value="__('tanggal ajuan')" />
                        <x-text-input class="mt-1 block w-full" id="tanggal_ajuan" name="tanggal_ajuan" type="date"
                            wire:model="tanggal_ajuan" autofocus autocomplete="tanggal_ajuan" />
                        <x-input-error class="mt-2" :messages="$errors->get('tanggal_ajuan')" />
                    </div>
                    <div>
                        <x-input-label for="unit" :value="__('unit')" />
                        <x-text-input class="mt-1 block w-full" id="unit" name="unit" type="text"
                            value="{{ auth()->user()->unit?->nama_unit }}" readonly />
                        <x-input-error class="mt-2" :messages="$errors->get('unit')" />
                    </div>
                    <div>
                        <x-input-label for="hps" :value="__('hps')" />
                        <x-money-input class="mt-1 block w-full" id="hps" wire:model.lazy="hps" autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('hps')" />
                    </div>
                    <div>
                        <x-input-label for="spesifikasi" :value="__('spesifikasi')" />
                        <x-textarea id="spesifikasi" name="keterangan" wire:model="spesifikasi" autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('spesifikasi')" />
                    </div>
                    <div>
                        <x-input-label for="file_rab" :value="__('Dokumen RAB (PDF)')" />
                        <x-file-input class="mt-1 block w-full" id="file_rab" wire:model="file_rab" autofocus />
                        <x-input-error class="mt-1" :messages="$errors->get('file_rab')" />
                    </div>
                    <div>
                        <x-input-label for="file_nota_dinas" :value="__('Dokumen Nota Dinas (PDF)')" />
                        <x-file-input class="mt-1 block w-full" id="file_nota_dinas" wire:model="file_nota_dinas"
                            autofocus />
                        <x-input-error class="mt-1" :messages="$errors->get('file_nota_dinas')" />
                    </div>
                    <div>
                        <x-input-label for="file_analisa_kajian" :value="__('Dokumen Analisa (PDF)')" />
                        <x-file-input class="mt-1 block w-full" id="file_analisa_kajian"
                            wire:model="file_analisa_kajian" autofocus />
                        <x-input-error class="mt-1" :messages="$errors->get('file_analisa_kajian')" />
                    </div>
                    <div>
                        <x-input-label for="jenis_ajuan" :value="__('Jenis Ajuan')" />
                        <x-radio-enum name="jenis_ajuan" enum="App\Enums\JenisAjuan" model="jenis_ajuan" />
                        <x-input-error class="mt-1" :messages="$errors->get('jenis_ajuan')" />
                    </div>
                </div>

                <!-- Tombol submit di luar grid, di bawah -->
                <div class="mt-6 flex items-center gap-4">
                    <x-primary-button>{{ __('Ajukan') }}</x-primary-button>
                    <x-action-message class="me-3" on="modal-stored">
                        {{ __('Pengajuan dikirim.') }}
                    </x-action-message>
                </div>
            </form>

        </div>
    </x-modal>
</section>
