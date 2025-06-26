<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-md sm:rounded-lg">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <x-section-header title="Monitor">
                        Berikut adalah monitor timeline pengajuan barang.
                    </x-section-header>
                </div>

                <div class="mt-6 space-y-6">
                    <div>
                        <livewire:admin.monitor.monitor-select />
                    </div>

                    <div class="overflow-x-auto py-2 px-4 sm:px-6 lg:px-8">
                        <div class="min-w-[640px] inline-block align-middle">
                            <livewire:admin.monitor.monitor-timeline-horizontal />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
