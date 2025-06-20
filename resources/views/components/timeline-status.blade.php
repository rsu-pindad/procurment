@props(['status'])

<div class="flex flex-col items-center w-40 text-center flex-shrink-0 relative">
    <div class="relative z-10 w-5 h-5 rounded-full border-4 border-white shadow cursor-default {{ $status['color'] }}"
        title="{{ $status['name'] }}"></div>

    <div class="mt-3">
        <p class="text-sm font-semibold text-gray-800">{{ $status['name'] }}</p>

        @if ($status['is_current'])
            <p class="text-xs text-green-600 font-medium">(Saat ini)</p>
        @elseif (!$status['is_passed'])
            <p class="text-xs text-gray-400 italic">Belum tercapai</p>
        @else
            @foreach ($status['audits'] as $log)
                <p class="text-xs text-gray-500">
                    {{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i') }}
                </p>
                <p class="text-xs text-gray-500">Oleh: {{ $log['user_name'] }}</p>
            @endforeach
        @endif
    </div>
</div>
