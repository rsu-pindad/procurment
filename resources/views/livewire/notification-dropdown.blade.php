<x-dropdown align="right" width="58">
    <x-slot name="trigger">
        <button
            class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
            <div class="relative">
                @svg('heroicon-o-bell', 'h-6 w-6')
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
        <div class="max-h-64 overflow-y-auto w-screen max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg px-2">
            @forelse ($notifications as $notification)
                <x-notification-item :notification="$notification" />
            @empty
                <p class="p-3 text-sm text-gray-500">belum ada notifikasi masuk</p>
            @endforelse
        </div>
    </x-slot>
</x-dropdown>
