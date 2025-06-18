<x-dropdown align="right" width="64">
    <x-slot name="trigger">
        <button
            class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">

            <div class="relative">
                {{-- SVG ikon lonceng --}}
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>

                @if (count($notifications))
                    <span
                        class="absolute -top-1.5 -right-1.5 bg-red-600 animate-pulse text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
                        {{ count($notifications) > 99 ? '99+' : count($notifications) }}
                    </span>
                @endif
            </div>
        </button>

    </x-slot>

    <x-slot name="content">
        {{-- <div class="max-h-64 overflow-y-auto inline-block min-w-[16rem] max-w-[22rem]"> --}}
        <div class="max-h-64 overflow-y-auto w-screen max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg px-2">
            @forelse ($notifications as $notification)
                <x-notification-item :notification="$notification" />
            @empty
                <p class="p-3 text-sm text-gray-500">belum ada notifikasi masuk</p>
            @endforelse
        </div>
    </x-slot>
</x-dropdown>
