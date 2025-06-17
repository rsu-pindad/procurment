@props(['notification'])

<div wire:click="markAsRead('{{ $notification->id }}')" wire:key="{{ $notification->id }}"
    {{ $attributes->merge(['class' => 'p-3 border-b cursor-pointer ' . ($notification->read_at ? 'bg-gray-100 text-gray-600' : 'bg-white text-gray-900 font-semibold')]) }}>

    <div class="flex items-center justify-between">
        <p class="text-sm">
            {{ $notification->data['message'] ?? 'Pesan kosong' }}
        </p>
        <a class="text-indigo-600 text-xs hover:underline" href="{{ route('ajuan') }}">
            Lihat
        </a>
    </div>

    <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
        <span>
            {{ carbon($notification['data']['created_at'])->diffForHumans() ?? '' }}
        </span>
        <form action="{{ route('ajuan', $notification['id']) }}" method="POST">
            @csrf
            @method('PATCH')
            <button class="hover:underline hover:text-gray-700 font-medium" type="submit">
                Tandai
            </button>
        </form>
    </div>

</div>
