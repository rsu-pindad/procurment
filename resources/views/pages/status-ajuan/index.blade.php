<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Status Ajuan') }}
        </h2>
    </x-slot>
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow rounded-md">
                <div class="sm:flex sm:items-center">
                    <x-section-header title="Tabel status ajuan">
                        Berikut adalah daftar status ajuan yang tersedia lengkap dengan detail informasi terkait
                        masing-masing
                        ajuan.
                    </x-section-header>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <livewire:admin.status-ajuan.status-ajuan-form />
                    </div>
                </div>
                <div class="mt-4 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-4 lg:px-6">
                            <livewire:status-ajuan-table />
                            <livewire:admin.status-ajuan.status-ajuan-manager />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
