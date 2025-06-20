@props(['notification'])

<div wire:key="{{ $notification->id }}"
    {{ $attributes->merge(['class' => 'p-3 border-b cursor-pointer ' . ($notification->read_at ? 'bg-gray-100 text-gray-600' : 'bg-white text-gray-900 font-semibold')]) }}>

    <div class="flex items-center justify-between">
        <p class="text-sm">
            {{ $notification->data['message'] ?? 'Pesan kosong' }}
        </p>
        <button class="text-indigo-600 text-xs hover:underline" type="button"
            wire:click="openNotification(@js($notification->id), @js($notification->data['ajuan_id']))">
            Lihat
        </button>

    </div>

    <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
        <span x-data="{
            timestamp: '{{ $notification['data']['created_at'] }}',
            get humanTime() {
                return window.dayjs(this.timestamp).fromNow();
            }
        }" x-init="setInterval(() => $el.innerText = humanTime, 60000)">
            {{ \Carbon\Carbon::parse($notification['data']['created_at'])->diffForHumans() }}
        </span>

        <button class="hover:underline hover:text-gray-700 font-medium" type="button"
            wire:click="markAsRead('{{ $notification->id }}')">
            Tandai sudah dibaca
        </button>
    </div>

</div>
