@props(['status'])

<div class="flex flex-col items-center w-full max-w-[10rem] text-center relative">

    <div class="w-5 h-5 top-2 rounded-full border-2 border-gray-200 shadow-md {{ $status['color'] }}"
        title="{{ $status['name'] }}" style="margin-bottom: 8px; position: relative;">
    </div>
    <!-- Status Info (teks) -->
    <div class="space-y-1" style="min-height: 4rem;">
        <p class="text-sm font-medium text-gray-800">{{ $status['name'] }}</p>
        @if ($status['is_current'])
            <p
                class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-600 ring-1 ring-inset ring-green-500/10">
                (Saat Ini)
            </p>
            <div class="space-y-2 text-left">
                @foreach ($status['audits'] as $log)
                    <p class="text-xs text-gray-500">
                        {{ carbon($log['created_at'])->translatedFormat('l, j F Y H:i') }},
                        oleh:
                        {{ $log['user_name'] }}
                    </p>
                @endforeach
            </div>
        @elseif (!$status['is_passed'])
            <p class="text-xs italic text-gray-400">Belum tercapai</p>
        @else
            <div class="space-y-2 text-left">
                @foreach ($status['audits'] as $log)
                    <p class="text-xs text-gray-500">
                        {{ carbon($log['created_at'])->translatedFormat('l, j F Y H:i') }},
                        oleh:
                        {{ $log['user_name'] }}
                    </p>
                @endforeach
            </div>
        @endif
    </div>
</div>
