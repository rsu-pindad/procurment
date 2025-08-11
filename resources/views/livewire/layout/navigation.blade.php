<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="bg-white border-b border-gray-100" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-9 fill-current text-gray-800" />
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if (auth()->user()->hasRole('admin'))
                    <x-nav-link :href="route('unit')" :active="request()->routeIs('unit')" wire:navigate>
                        {{ __('Unit') }}
                    </x-nav-link>
                    <x-nav-link :href="route('kategori')" :active="request()->routeIs('kategori')" wire:navigate>
                        {{ __('Kategori') }}
                    </x-nav-link>
                    @endif
                    @if (auth()->user()->hasRole(['pengadaan', 'pegawai']))
                    <x-nav-link :href="route('ajuan')" :active="request()->routeIs('ajuan')" wire:navigate>
                        {{ __('Ajuan') }}
                    </x-nav-link>
                    @endif
                    @if (auth()->user()->hasRole(['pengadaan', 'admin']))
                    <x-nav-link :href="route('status-ajuan')" :active="request()->routeIs('status-ajuan')" wire:navigate>
                        {{ __('Status Ajuan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('vendors')" :active="request()->routeIs('vendors')" wire:navigate>
                        {{ __('Vendors') }}
                    </x-nav-link>
                    <x-nav-link :href="route('manajemen.user')" :active="request()->routeIs('manajemen.user')" wire:navigate>
                        {{ __('Manajemen User') }}
                    </x-nav-link>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-4 ms-auto">
                <div class="sm:flex sm:items-center sm:ms-6 space-x-1">
                    <livewire:notification-dropdown />
                </div>
                <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-1">
                    <x-dropdown class="w-auto">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name=$event.detail.name">
                                </div>
                                <div class="ms-1">
                                    @svg('heroicon-m-chevron-down', 'fill-current h-4 w-4')
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" class="inline-flex items-center" wire:navigate>
                                @svg('heroicon-o-cog-6-tooth', 'h-4 w-4 mr-2'){{ __('Profile') }}
                            </x-dropdown-link>
                            <hr class="my-2">
                            <button class="w-full text-start" wire:click="logout">
                                <x-dropdown-link class="inline-flex items-center">
                                    @svg('heroicon-o-arrow-right-on-rectangle','h-4 w-4 mr-2'){{ __('Keluar') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
            <div class="-me-2 flex items-center sm:hidden">
                <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="open = ! open">
                    @svg('heroicon-o-bars-3', 'h-6 w-6')
                </button>
            </div>
        </div>
    </div>
    <div class="hidden sm:hidden" :class="{ 'block': open, 'hidden': !open }">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if (auth()->user()->hasRole('admin'))
            <x-responsive-nav-link :href="route('unit')" :active="request()->routeIs('unit')" wire:navigate>
                {{ __('Unit') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('kategori')" :active="request()->routeIs('kategori')" wire:navigate>
                {{ __('Kategori') }}
            </x-responsive-nav-link>
            @endif
            @if (auth()->user()->hasRole(['pengadaan', 'pegawai']))
            <x-responsive-nav-link :href="route('ajuan')" :active="request()->routeIs('ajuan')" wire:navigate>
                {{ __('Ajuan') }}
            </x-responsive-nav-link>
            @endif
            @if (auth()->user()->hasRole(['pengadaan', 'admin']))
            <x-responsive-nav-link :href="route('status-ajuan')" :active="request()->routeIs('status-ajuan')" wire:navigate>
                {{ __('Status Ajuan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('vendors')" :active="request()->routeIs('vendors')" wire:navigate>
                {{ __('Vendors') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('manajemen.user')" :active="request()->routeIs('manajemen.user')" wire:navigate>
                {{ __('Manajemen User') }}
            </x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <button class="w-full text-start" wire:click="logout">
                    <x-responsive-nav-link>
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
