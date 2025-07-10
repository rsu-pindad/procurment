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
            'hps' => ['required', 'numeric', 'min:10000', 'max:500000000'],
            'spesifikasi' => ['required', 'string', 'max:255'],
            'jenis_ajuan' => ['required'],
            'file_nota_dinas' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'file_rab' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'file_analisa_kajian' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'realisasi' => ['nullable', 'date'],
        ];

        $rkap = [
            'kategori' => 'required',
        ];

        if ($this->rkapForm) {
            $finalForm = array_merge($jenisForm, $rkap);
            return $finalForm;
        }

        return $jenisForm;
    }

    protected function messages()
    {
        $jenisForm = [
            'tanggal_ajuan.required' => __('validation.tanggal_ajuan.required'),
            'tanggal_ajuan.date' => __('validation.tanggal_ajuan.date'),
            'produk_ajuan.required' => __('validation.produk_ajuan.required'),
            'produk_ajuan.min' => __('validation.produk_ajuan.min'),
            'produk_ajuan.max' => __('validation.produk_ajuan.max'),
            'unit.required' => __('validation.unit.required'),
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
            'realisasi.date' => __('validation.realisasi.date'),
        ];
        $rkap = [
            'kategori.required' => __('validation.kategori.required'),
        ];

        if ($this->rkapForm) {
            $finalForm = array_merge($jenisForm, $rkap);
            return $finalForm;
        }
        return $jenisForm;
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
        // $this->hps = preg_replace('/\D/', '', $value);
        $cleaned = preg_replace('/\D/', '', $value);
        $this->hps = (int) $cleaned;
    }

    #[Computed]
    public function updatedJenisAjuan($value)
    {
        if ($this->jenis_ajuan === 'NONRKAP') {
            $this->rkapForm = false;
            $this->kategori = null;
        } else {
            $this->rkapForm = true;
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
        // ini untuk user menginfikan ke pengadaan
        \App\Models\User::withRole('pengadaan')
            ->get()
            ->each(function ($pengadaan) use ($ajuan) {
                $pengadaan->notify(new \App\Notifications\PengajuanUserNotification($ajuan));
                // Siarkan event untuk realtime update
                // event(new \App\Events\NotificationReceived($pengadaan->notifications()->latest()->first(), $pengadaan->id));
            });
    }

    public function store(): void
    {
        $validated = $this->validate();
        // $pathRab = $this->file_rab->store('rab');
        $pathNodin = $this->file_nota_dinas ? $this->file_nota_dinas->store('nodin') : null;
        // $pathAnalisa = $this->file_analisa_kajian->store('analisa');
        $pathRab = $this->file_rab ? $this->file_rab->store('rab') : null;
        // $pathNodin = $this->file_nota_dinas ? $this->file_nota_dinas->store('nodin') : null;
        $pathAnalisa = $this->file_analisa_kajian ? $this->file_analisa_kajian->store('analisa') : null;
        $realisasi = \App\Models\Admin\StatusAjuan::where('nama_status_ajuan', 'Pelaksanaan / Delivery')->get();

        if (count($realisasi) > 0) {
            $realisasi = $realisasi->first()->id;

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

            // Attach status_ajuan ke pivot dengan kolom realisasi dan result_realisasi
            $ajuan->statusHistories()->attach($realisasi, [
                'updated_by' => auth()->id(),
                'realisasi' => $this->realisasi,
                'result_realisasi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ini untuk pengadaan menginfokan ke unit
            $rolePegawai = \App\Models\User::withRole('pegawai')->where('unit_id', $this->unit)->get();
            if (count($rolePegawai) > 0) {
                $this->informasiUnit($rolePegawai, $ajuan);
            }

            $this->dispatch('modal-stored', name: 'pengajuan');
            $this->dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table');
            $this->reset();
        }
    }
};

?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Ajuan') }}
        </h2>
    </x-slot>

    <div class="p-4 space-y-4">
        <form wire:submit="store">
            <!-- Grid untuk input fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <x-radio-enum class="mt-1 block w-full" name="jenis_ajuan" enum="App\Enums\JenisAjuan" model="jenis_ajuan" wire:model.live="jenis_ajuan" />
                    <x-input-error class="mt-2" :messages="$errors->get('jenis_ajuan')" />
                </div>
                @if ($rkapForm)
                <div class="col-span-2">
                    <x-input-label for="kategori" :value="__('kategori')" required />
                    <livewire:utility.remote-select id="kategori" name="kategori" model="App\Models\Admin\KategoriPengajuan" label="nama_kategori" wire:model.lazy="kategori" />
                    <x-input-error class="mt-2" :messages="$errors->get('kategori')" />
                </div>
                @endif
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
                <div>
                    <x-input-label for="realisasi" :value="__('tanggal estimasi realisasi ke delivery')" />
                    <x-text-input class="mt-1 block w-full" id="realisasi" type="date" wire:model="realisasi" />
                    <x-input-error class="mt-2" :messages="$errors->get('realisasi')" />
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
</section>
