<?php

use Livewire\Volt\Component;
use App\Models\Ajuan;
use App\Models\Admin\KategoriPengajuan;
use Livewire\WithFileUploads;
use Livewire\Attributes\{Layout, Title, Computed};

new
    #[Layout('components.layouts.app')] #[Title('edit ajuan')]
    class extends Component
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

        #[Locked]
        public $id;

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

            if ($this->rkapForm) {
                $finalForm = array_merge($jenisForm, $rkap);
                return $finalForm;
            }

            return $jenisForm;
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
                // 'file_rab.required' => __('validation.file_rab.required'),
                'file_rab.file' => __('hanya menerima file'),
                'file_rab.mimes' => __('file harus berformat .pdf'),
                'file_rab.max' => __('ukuran file maksimal 5MB'),
                // 'file_nota_dinas.required' => __('validation.file_nota_dinas.required'),
                'file_nota_dinas.file' => __('hanya menerima file'),
                'file_nota_dinas.mimes' => __('file harus berformat .pdf'),
                'file_nota_dinas.max' => __('ukuran file maksimal 5MB'),
                // 'file_analisa_kajian.required' => __('validation.file_analisa_kajian.required'),
                'file_analisa_kajian.file' => __('hanya menerima file'),
                'file_analisa_kajian.mimes' => __('file harus berformat .pdf'),
                'file_analisa_kajian.max' => __('ukuran file maksimal 5MB'),
                'jenis_ajuan.required' => __('mohon pilih jenis ajuan'),
                'realisasi.date' => __('format tanggal tidak cocok'),
            ];
            $rkap = [
                'kategori.required' => __('mohon pilih kategori'),
            ];

            if ($this->rkapForm) {
                $finalForm = array_merge($jenisForm, $rkap);
                return $finalForm;
            }
            return $jenisForm;
        }

        public function mount(Ajuan $ajuan)
        {
            $this->id = $ajuan->id;
            $this->tanggal_ajuan = $ajuan->tanggal_ajuan;
            $this->produk_ajuan = $ajuan->produk_ajuan;
            $this->spesifikasi = $ajuan->spesifikasi;
            $this->hps = $ajuan->hps;
            $this->unit = $ajuan->units_id;
            $this->jenis_ajuan = $ajuan->jenis_ajuan;

            // $this->kategori = KategoriPengajuan::where('ajuan_id',$ajuan->id)->get()->first()->id;
            $this->kategori = $this->getKategoriPengajuansProperty($ajuan->id);
            // dd($this->jenis_ajuan);
            if ($this->jenis_ajuan === "NONRKAP") {
                $this->rkapForm = false;
            }
        }

        #[Computed]
        public function getKategoriPengajuansProperty($id)
        {
            // Menggunakan eager loading di computed property untuk memuat kategori_pengajuans
            $ajuan = Ajuan::with('kategori_pengajuans')->find($id); // Load berdasarkan ID yang sudah ada
            return $ajuan ? $ajuan->kategori_pengajuans->pluck('id')->toArray() : [];
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
        public function kategoris()
        {
            return \App\Models\Admin\KategoriPengajuan::all();
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
                // $this->kategori = $this->;
            } else {
                $this->rkapForm = true;
            }
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

                $ajuan = Ajuan::find($this->id);
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
                    $ajuan->kategori_pengajuans()->sync($this->kategori);
                }

                if ($this->jenis_ajuan === "NONRKAP") {
                    $ajuan->kategori_pengajuans()->detach($this->kategori);
                }

                // Attach status_ajuan ke pivot dengan kolom realisasi dan result_realisasi
                $ajuan->statusHistories()->attach($realisasi, [
                    'updated_by' => auth()->id(),
                    'realisasi' => $this->realisasi,
                    'result_realisasi' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->dispatch('modal-edited', name: 'pengajuan');
                // $this->dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table');
                // $this->reset();
            }
        }

        public function goBack(): void
        {
            $this->redirect('/ajuan', navigate: true);
        }
    }; ?>

<section>
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="sm:flex sm:items-center">
                    <x-section-header title="Form edit ajuan">
                        silahkan edit form daftar ajuan yang tersedia dengan detail informasi terkait
                        masing-masing
                        ajuan.
                    </x-section-header>
                    <button class="mt-4 sm:mt-0 px-4 py-2 bg-blue-100 text-sm text-blue-700 rounded-md hover:bg-blue-200 transition" wire:click="goBack">
                        @svg('heroicon-s-arrow-left', 'w-5 h-5 inline-flex mx-2')
                    </button>
                </div>
                <div class="mt-4 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-4 lg:px-6">
                            <section>
                                <x-slot name="header">
                                    <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                                        {{ __('Edit Ajuan') }}
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
                                                <x-select-input class="mt-1 block w-full" id="unit" name="unit" wire:model="unit">
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
                                            @if ($rkapForm)
                                            <div class="col-span-2">
                                                <x-input-label for="kategori" :value="__('kategori')" required />
                                                <x-select-input class="mt-1 block w-full" id="kategori" name="kategori" wire:model.lazy="kategori">
                                                    <option value="">{{ __('pilih kategori') }}</option>
                                                    @foreach ($this->kategoris() as $kategori)
                                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                                    @endforeach
                                                </x-select-input>
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
                                            <x-primary-button>@svg('heroicon-s-pencil-square','w-5 h-5 mr-2'){{ __('Edit') }}</x-primary-button>
                                            <x-action-message class="me-3" on="modal-edited">
                                                {{ __('Pengajuan diperbarui.') }}
                                            </x-action-message>
                                        </div>
                                    </form>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
