<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Beranda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (auth()->user()->hasRole('pengadaan'))
                <livewire:beranda.chart-hps />
            @endif
            <livewire:beranda.chart-status />
            <livewire:beranda.chart-tempo />
        </div>
    </div>
</x-layouts.app>
