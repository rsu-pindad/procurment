<div class="max-w-7xl bg-white rounded-md shadow border border-gray-200 p-5">
    <span
        class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800">
        <svg class="h-3 w-3 fill-gray-600" role="img" aria-hidden="true" viewBox="0 0 6 6">
            <circle cx="3" cy="3" r="3" />
        </svg>
        Ajuan
    </span>

    <h4 class="mt-3 text-lg font-semibold text-gray-900">
        {{ $produkAjuan }}
    </h4>

    @if ($histories && $realisasiTanggal && $realisasiSelisih)
        <div class="mt-3 text-base text-gray-700">
            <p class="font-semibold">Estimasi realisasi menuju delivery:</p>
            <p>
                <span class=" text-gray-900">{{ $realisasiSelisih }}</span>
                <span class="ml-1 text-blue-600">{{ $realisasiTanggal }}</span>
            </p>
        </div>
    @endif
    @if (auth()->user()->hasRole('pengadaan'))
        <div class="mt-3 text-sm text-gray-600 flex flex-row">
            <ul class="grow">
                <li>tanggal pengajuan : {{ $produk->tanggal_ajuan }}</li>
                <li>hps : {{ $produk->hps }}</li>
                <li>spefifikasi : {{ $produk->spesifikasi }}</li>
                <li>unit : {{ $produk->unit->nama_unit }}</li>
            </ul>
            <ul class="grow divide-y divide-gray-100 rounded-md border border-gray-200" role="list">
                <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">{{ $produk->jenis_ajuan }}</li>
                <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">
                    <div class="flex w-0 flex-1 items-center">
                        <svg class="size-5 shrink-0 text-gray-400" aria-hidden="true" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="ml-4 flex min-w-0 flex-1 gap-2">
                            <span class="truncate font-medium">
                                {{ $produk->file_rab ? basename($produk->file_rab) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4 shrink-0">
                        <a class="font-medium text-indigo-600 hover:text-indigo-500"
                            href="{{ $produk->file_rab ? route('rab.show', substr($produk->file_rab, 4)) : '#' }}">
                            {{ $produk->file_rab ? 'Lihat' : '-' }}
                        </a>
                    </div>
                </li>
                <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">
                    <div class="flex w-0 flex-1 items-center">
                        <svg class="size-5 shrink-0 text-gray-400" aria-hidden="true" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="ml-4 flex min-w-0 flex-1 gap-2">
                            <span class="truncate font-medium">
                                {{ $produk->file_nota_dinas ? basename($produk->file_nota_dinas) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4 shrink-0">
                        <a class="font-medium text-indigo-600 hover:text-indigo-500"
                            href="{{ $produk->file_nota_dinas ? route('nodin.show', basename($produk->file_nota_dinas)) : '#' }}">
                            {{ $produk->file_nota_dinas ? 'Lihat' : '-' }}
                        </a>
                    </div>
                </li>
                <li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm/6">
                    <div class="flex w-0 flex-1 items-center">
                        <svg class="size-5 shrink-0 text-gray-400" aria-hidden="true" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="ml-4 flex min-w-0 flex-1 gap-2">
                            <span class="truncate font-medium">
                                {{ $produk->file_analisa_kajian ? basename($produk->file_analisa_kajian) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4 shrink-0">
                        <a class="font-medium text-indigo-600 hover:text-indigo-500"
                            href="{{ $produk->file_analisa_kajian ? route('analisa.show', basename($produk->file_analisa_kajian)) : '#' }}">
                            {{ $produk->file_analisa_kajian ? 'Lihat' : '-' }}
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    @endif
</div>
