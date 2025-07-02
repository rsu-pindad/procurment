@props(['status'])

<div class="flex flex-col items-center w-36 sm:w-40 text-center flex-shrink-0 relative" style="z-index: 20;">
    <!-- Dot: tetap posisi normal tapi pastikan z-index di atas garis -->
    <div class="w-5 h-5 rounded-full border-4 border-white shadow-md {{ $status['color'] }}" title="{{ $status['name'] }}"
        style="margin-bottom: 8px; z-index: 20; position: relative;">
    </div>

    <!-- Status Info (teks) -->
    <div class="space-y-1" style="min-height: 4rem;">
        <p class="text-sm font-medium text-gray-800">{{ $status['name'] }}</p>

        @if ($status['is_current'])
            <p class="text-xs font-semibold text-green-600">(Saat ini)</p>
            @foreach ($status['audits'] as $log)
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i') }}
                </p>
                <p class="text-xs text-gray-500">Oleh: {{ $log['user_name'] }}</p>
            @endforeach
        @elseif (!$status['is_passed'])
            <p class="text-xs italic text-gray-400">Belum tercapai</p>
        @else
            @foreach ($status['audits'] as $log)
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i') }}
                </p>
                <p class="text-xs text-gray-500">Oleh: {{ $log['user_name'] }}</p>
            @endforeach
        @endif
    </div>
</div>
