<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Ajuan') }}
        </h2>
    </x-slot>
    @if (auth()->user()->hasRole('pengadaan'))
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow rounded-md">
                <div class="sm:flex sm:items-center">
                    <x-section-header title="Form ajuan">
                        silahkan isi form daftar ajuan yang tersedia dengan detail informasi terkait
                        masing-masing
                        pengajuan.
                    </x-section-header>
                </div>
                <div class="mt-4 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-4 lg:px-6">
                            <livewire:ajuan.ajuan-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow rounded-md">
                <div class="sm:flex sm:items-center">
                    <x-section-header title="Tabel Ajuan">
                        Berikut adalah daftar ajuan yang tersedia dengan detail informasi terkait masing-masing ajuan.
                    </x-section-header>
                    @if (auth()->user()->hasRole('pengadaan'))
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <livewire:import.ajuan-import />
                    </div>
                    @endif
                </div>
                <div class="mt-4 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-4 lg:px-6">
                            <livewire:user-ajuan-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        const notyf = new Notyf({
            duration: 10000,
            position: {
                x: 'center',
                y: 'center',
            },
            ripple: true,
            dismissible: true,
            types: [{
                type: 'info',
                background: 'orange',
                icon: false
            }]
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('info-hapus', (event) => {
                notyf.open({
                    type: 'info',
                    message: event.message
                });
                Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table');
            });
            Livewire.on('modal-stored', (event) => {
                notyf.open({
                    type: 'success',
                    message: 'Pengajuan berhasil dikirim.'
                });
            });
            Livewire.on('modal-edited', (event) => {
                notyf.open({
                    type: 'success',
                    message: 'Pengajuan berhasil diperbarui.'
                });
            });
        });
    </script>
</x-layouts.app>
