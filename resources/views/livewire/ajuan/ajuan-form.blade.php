<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use Livewire\WithFileUploads;
use App\Enums\JenisAjuan;

new class extends Component
{
    use WithFileUploads;

    public $tanggal_ajuan;
    public $produk_ajuan;
    public $hps = '0';
    public $spesifikasi;
    public $file_rab;
    public $file_nota_dinas;
    public $file_analisa_kajian;
    public $unit;
    public $jenis_ajuan;
    public $kategori;
    public bool $rkapForm = true;
    public $realisasi;

    public function getListeners()
    {
        return [
            'setSelectedkategori' => 'getSelectedKategori',
        ];
    }

    protected function rules()
    {
        $jenisForm = [
            'tanggal_ajuan' => ['required', 'date'],
            'produk_ajuan' => ['required', 'string', 'min:3', 'max:255'],
            'unit' => ['required'],
            'hps' => ['required', 'numeric', 'min:10000', 'max:5000000000'],
            'spesifikasi' => ['nullable', 'string', 'max:255'],
            'jenis_ajuan' => ['required'],
            'file_nota_dinas' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'file_rab' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'file_analisa_kajian' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'realisasi' => ['nullable', 'date'],
        ];

        $rkap = [
            'kategori' => 'required',
        ];

        return $this->rkapForm ? array_merge($jenisForm, $rkap) : $jenisForm;
    }

    protected function messages()
    {
        $jenisForm = [
            'tanggal_ajuan.required' => __('mohon isi tanggal ajuan'),
            'tanggal_ajuan.date' => __('format tanggal tidak cocok'),
            'produk_ajuan.required' => __('mohon isi produk / jasa ajuan'),
            'produk_ajuan.min' => __('nama produk / jasa, minimal 3 karakter'),
            'produk_ajuan.max' => __('nama produk / jasa, maksimal 255 karakter'),
            'unit.required' => __('mohon pilih unit'),
            'hps.required' => __('mohon isi hps'),
            'hps.numeric' => __('hanya angka'),
            'hps.min' => __('hps minimal 10.000'),
            'hps.max' => __('hps maksimal 5.000.000.000'),
            'spesifikasi.string' => __('hanya menerima huruf dan angka'),
            'spesifikasi.max' => __('spesifikasi maksimal 255 karakter'),
            'file_rab.file' => __('hanya menerima file'),
            'file_rab.mimes' => __('file harus berformat .pdf'),
            'file_rab.max' => __('ukuran file maksimal 5MB'),
            'file_nota_dinas.file' => __('hanya menerima file'),
            'file_nota_dinas.mimes' => __('file harus berformat .pdf'),
            'file_nota_dinas.max' => __('ukuran file maksimal 5MB'),
            'file_analisa_kajian.file' => __('hanya menerima file'),
            'file_analisa_kajian.mimes' => __('file harus berformat .pdf'),
            'file_analisa_kajian.max' => __('ukuran file maksimal 5MB'),
            'jenis_ajuan.required' => __('mohon pilih jenis ajuan'),
            'realisasi.date' => __('format tanggal tidak cocok'),
        ];

        $rkap = [
            'kategori.required' => __('mohon pilih kategori'),
        ];

        return $this->rkapForm ? array_merge($jenisForm, $rkap) : $jenisForm;
    }

    public function mount()
    {
        $this->tanggal_ajuan = now()->format('Y-m-d');
        $this->jenis_ajuan = JenisAjuan::RKAP->value;
        $this->realisasi = carbon($this->tanggal_ajuan)->addMonth(2)->format('Y-m-d');
    }

    #[Reactive]
    public function getSelectedKategori($id)
    {
        $this->kategori = $id;
    }

    #[Computed]
    public function units()
    {
        return \App\Models\Admin\Unit::all();
    }

    #[Computed]
    public function updatedHps($value)
    {
        $this->hps = (int) preg_replace('/\D/', '', $value);
    }

    #[Computed]
    public function updatedJenisAjuan($value)
    {
        if ($value === 'NONRKAP') {
            $this->rkapForm = false;
            $this->kategori = null;
            $this->dispatch('resetkategoriSelect');
        } else {
            $this->rkapForm = true;
            $this->dispatch('refreshkategoriSelect');
        }
    }


    public function informasiUnit($data = [], $ajuan): void
    {
        $data->each(function ($pegawai) use ($ajuan) {
            $pegawai->notify(new \App\Notifications\PengajuanUnitNotification($ajuan));
        });
    }

    public function inform()
    {
        \App\Models\User::withRole('pengadaan')
            ->get()
            ->each(function ($pengadaan) use ($ajuan) {
                $pengadaan->notify(new \App\Notifications\PengajuanUserNotification($ajuan));
            });
    }

    public function store(): void
    {
        $validated = $this->validate();
        $pathNodin = $this->file_nota_dinas ? $this->file_nota_dinas->store('nodin') : null;
        $pathRab = $this->file_rab ? $this->file_rab->store('rab') : null;
        $pathAnalisa = $this->file_analisa_kajian ? $this->file_analisa_kajian->store('analisa') : null;
        $realisasi = \App\Models\Admin\StatusAjuan::where('nama_status_ajuan', 'Pelaksanaan / Delivery')->first()?->id;

        if ($realisasi) {
            $ajuan = new Ajuan();
            $ajuan->units_id = $this->unit;
            $ajuan->tanggal_ajuan = $this->tanggal_ajuan;
            $ajuan->produk_ajuan = $this->produk_ajuan;
            $ajuan->hps = (int) $this->hps;
            $ajuan->spesifikasi = $this->spesifikasi;
            $ajuan->file_rab = $pathRab;
            $ajuan->file_nota_dinas = $pathNodin;
            $ajuan->file_analisa_kajian = $pathAnalisa;
            $ajuan->jenis_ajuan = $this->jenis_ajuan;
            $ajuan->tanggal_update_terakhir = now();
            $ajuan->status_ajuans_id = 1;
            $ajuan->users_id = auth()->id();
            $ajuan->tahun_ajuan = now()->format('Y');
            $ajuan->save();

            if (!empty($this->kategori)) {
                $ajuan->kategori_pengajuans()->attach($this->kategori);
            }

            $ajuan->statusHistories()->attach($realisasi, [
                'updated_by' => auth()->id(),
                'realisasi' => $this->realisasi,
                'result_realisasi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $rolePegawai = \App\Models\User::withRole('pegawai')->where('unit_id', $this->unit)->get();
            if ($rolePegawai->isNotEmpty()) {
                $this->informasiUnit($rolePegawai, $ajuan);
            }

            $this->dispatch('modal-stored', name: 'pengajuan');
            $this->reset();
            $this->tanggal_ajuan = now()->format('Y-m-d');
            $this->jenis_ajuan = JenisAjuan::RKAP->value;
            $this->realisasi = carbon($this->tanggal_ajuan)->addMonth(2)->format('Y-m-d');
        }
    }
};

?>

<section>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'open-create-ajuan')">{{ __('Tambah Ajuan') }}
    </x-primary-button>
    <x-modal name="open-create-ajuan" :show="$errors->isNotEmpty()" maxWidth="max" focusable wire:key="modal-create-ajuan">
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Form Ajuan') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Masukkan informasi ajuan pada form di bawah ini.') }}
                </p>
            </header>
            <form class="space-y-6 space-x-3" wire:submit="store">
                <div class="grid grid-cols-4 gap-6">
                    <div>
                        <x-input-label for="tanggal_ajuan" :value="__('tanggal ajuan')" required />
                        <x-text-input class="mt-1 block w-full" id="tanggal_ajuan" name="tanggal_ajuan" type="date" wire:model="tanggal_ajuan" autofocus autocomplete="tanggal_ajuan" />
                        <x-input-error class="mt-2" :messages="$errors->get('tanggal_ajuan')" />
                    </div>
                    <div>
                        <x-input-label for="produk_ajuan" :value="__('produk atau jasa ajuan')" required />
                        <x-text-input class="mt-1 block w-full" id="produk_ajuan" name="produk_ajuan" type="text" wire:model="produk_ajuan" autofocus autocomplete="produk_ajuan" placeholder="nama jasa/produk" />
                        <x-input-error class="mt-2" :messages="$errors->get('produk_ajuan')" />
                    </div>
                    <div>
                        <x-input-label for="unit" :value="__('unit')" required />
                        <x-select-input class="mt-1 block w-full" id="unit" name="unit" wire:model.lazy="unit">
                            <option value="">{{ __('pilih unit') }}</option>
                            @foreach ($this->units() as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error class="mt-2" :messages="$errors->get('unit')" />
                    </div>
                    <div>
                        <x-input-label for="hps" :value="__('hps')" required />
                        <x-money-input class="mt-1 block w-full" id="hps" wire:model.lazy="hps" autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('hps')" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="spesifikasi" :value="__('spesifikasi')" />
                        <x-textarea id="spesifikasi" name="keterangan" wire:model="spesifikasi" autofocus placeholder="spefisikasi jasa/produk" />
                        <x-input-error class="mt-2" :messages="$errors->get('spesifikasi')" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="jenis_ajuan" :value="__('jenis ajuan')" required />
                        <x-radio-enum class="mt-1 block w-full" name="jenis_ajuan" enum="App\Enums\JenisAjuan" model="jenis_ajuan" wire:model.lazy="jenis_ajuan" />
                        <x-input-error class="mt-2" :messages="$errors->get('jenis_ajuan')" />
                    </div>
                    <div class="col-span-2 @if(!$rkapForm) hidden @endif">
                        <x-input-label for="kategori" :value="__('kategori')" required />
                        <livewire:utility.remote-select id="kategori" name="kategori" model="App\Models\Admin\KategoriPengajuan" label="nama_kategori" wire:model.lazy="kategori" wire:key="kategori-select" :disabled="!$rkapForm" />
                        <x-input-error class="mt-2" :messages="$errors->get('kategori')" />
                    </div>
                    <div class="@if(!$rkapForm)col-span-4 @else col-span-2 @endif">
                        <x-input-label for="realisasi" :value="__('tanggal estimasi realisasi ke delivery')" />
                        <x-text-input class="mt-1 block w-full" id="realisasi" type="date" wire:model="realisasi" />
                        <x-input-error class="mt-2" :messages="$errors->get('realisasi')" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="file_nota_dinas" :value="__('dokumen nota dinas (pdf)')" />
                        <x-file-input class="mt-1 block w-full" id="file_nota_dinas" wire:model="file_nota_dinas" autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('file_nota_dinas')" />
                    </div>
                    @if (!$rkapForm)
                    <div>
                        <x-input-label for="file_rab" :value="__('dokumen rab (pdf)')" />
                        <x-file-input class="mt-1 block w-full" id="file_rab" wire:model="file_rab" autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('file_rab')" />
                    </div>
                    <div>
                        <x-input-label for="file_analisa_kajian" :value="__('dokumen analisa (pdf)')" />
                        <x-file-input class="mt-1 block w-full" id="file_analisa_kajian" wire:model="file_analisa_kajian" autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('file_analisa_kajian')" />
                    </div>
                    @endif
                </div>
                <div class="mt-6 flex items-center gap-4">
                    <x-primary-button>@svg('heroicon-o-paper-airplane','w-5 h-5 mr-2'){{ __('Ajukan') }}</x-primary-button>
                    <x-action-message class="me-3" on="modal-stored">
                        {{ __('Pengajuan dikirim.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
