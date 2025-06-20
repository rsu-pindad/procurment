@props([
    'status', // associative array dari $statusesWithAudit
    'produkAjuan' => null,
])

<div class="relative border-l-4 {{ $status['border_color'] }} ml-4 space-x-4">
    <div
        class="relative ml-6 my-0 before:absolute before:top-2 before:left-1 before:bottom-0 before:w-px {{ str_replace('bg', 'before:bg', $status['circle_color']) }}">
        {{-- Circle --}}
        <div class="absolute w-4 h-4 {{ $status['circle_color'] }} rounded-full -left-2.5 top-1 z-10"></div>

        {{-- Status info --}}
        <h4 class="ml-4 text-lg font-semibold text-gray-800">
            {{ $status['name'] }}
            @if ($status['is_current'])
                <span class="ml-1 text-sm text-green-600">(Status saat ini)</span>
            @endif
        </h4>

        @if ($status['is_passed'])
            @foreach ($status['audits'] as $audit)
                <div class="ml-4">
                    <time
                        class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($audit['created_at'])->format('d M Y H:i') }}</time>
                    <p class="text-sm text-gray-600">Oleh: {{ $audit['user_name'] }}</p>
                    <p class="text-sm text-gray-600 mb-2">Produk: {{ $produkAjuan }}</p>
                    <hr>
                </div>
            @endforeach
        @else
            <p class="ml-4 text-sm text-gray-400 italic">Belum sampai ke tahap ini.</p>
        @endif
    </div>
</div>
