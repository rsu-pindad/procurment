<div class="max-w-7xl bg-white rounded-md shadow border border-gray-200 p-5">
    <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 py-2 px-3 text-md font-semibold uppercase text-gray-800">
        @svg('heroicon-o-document-text', 'h-5 w-5 mr-2 inline-flex')
        {{ $produkAjuan }}
    </span>

    @if ($histories && $realisasiTanggal && $realisasiSelisih)
    <div class="mt-3 text-base text-gray-700 gap-y-2">
        <p class="font-semibold">Estimasi realisasi menuju delivery:</p>
        <p>
            <span class=" text-gray-900">{{ $realisasiSelisih }}</span>
            <span class="ml-1 text-blue-600">{{ $realisasiTanggal }}</span>
        </p>
    </div>
    @endif
    @if (auth()->user()->hasRole('pengadaan'))
    <div class="mt-3 text-sm text-gray-600 flex flex-row gap-y-2">
        <ul class="grow space-y-2">
            <li>tanggal pengajuan : {{ $produk->tanggal_ajuan }}</li>
            <li>hps : {{ number_format($produk->hps,2, ',', '.') }}</li>
            <li>spefifikasi : {{ $produk->spesifikasi }}</li>
            <li>unit : {{ $produk->unit->nama_unit }}</li>
        </ul>
        <ul class="grow divide-y divide-gray-100 rounded-md border border-gray-200" role="list">
            <li class="flex items-center justify-between py-2 pl-4 pr-5 text-base bg-blue-200 font-semibold">{{ $produk->jenis_ajuan }}</li>
            <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">
                <div class="flex w-0 flex-1 items-center">
                    @svg('heroicon-c-paper-clip', 'w-5 h-5 text-gray-400 inline-flex')
                    <div class="ml-4 flex min-w-0 flex-1 gap-2">
                        <span class="truncate font-medium">
                            {{ $produk->file_rab ? basename($produk->file_rab) : '-' }}
                        </span>
                    </div>
                </div>
                <div class="ml-4 shrink-0">
                    <a class="font-medium text-indigo-600 hover:text-indigo-500" href="{{ $produk->file_rab ? route('rab.show', substr($produk->file_rab, 4)) : '#' }}">
                        {{ $produk->file_rab ? 'Lihat' : '-' }}
                    </a>
                </div>
            </li>
            <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">
                <div class="flex w-0 flex-1 items-center">
                    @svg('heroicon-c-paper-clip', 'w-5 h-5 text-gray-400 inline-flex')
                    <div class="ml-4 flex min-w-0 flex-1 gap-2">
                        <span class="truncate font-medium">
                            {{ $produk->file_nota_dinas ? basename($produk->file_nota_dinas) : '-' }}
                        </span>
                    </div>
                </div>
                <div class="ml-4 shrink-0">
                    <a class="font-medium text-indigo-600 hover:text-indigo-500" href="{{ $produk->file_nota_dinas ? route('nodin.show', basename($produk->file_nota_dinas)) : '#' }}">
                        {{ $produk->file_nota_dinas ? 'Lihat' : '-' }}
                    </a>
                </div>
            </li>
            <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">
                <div class="flex w-0 flex-1 items-center">
                    @svg('heroicon-c-paper-clip', 'w-5 h-5 text-gray-400 inline-flex')
                    <div class="ml-4 flex min-w-0 flex-1 gap-2">
                        <span class="truncate font-medium">
                            {{ $produk->file_analisa_kajian ? basename($produk->file_analisa_kajian) : '-' }}
                        </span>
                    </div>
                </div>
                <div class="ml-4 shrink-0">
                    <a class="font-medium text-indigo-600 hover:text-indigo-500" href="{{ $produk->file_analisa_kajian ? route('analisa.show', basename($produk->file_analisa_kajian)) : '#' }}">
                        {{ $produk->file_analisa_kajian ? 'Lihat' : '-' }}
                    </a>
                </div>
            </li>
        </ul>
    </div>
    @endif
</div>
