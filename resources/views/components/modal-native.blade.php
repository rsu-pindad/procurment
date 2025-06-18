@props(['name', 'show' => false, 'maxWidth' => '2xl'])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];
@endphp

@if ($show)
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 bg-gray-500 opacity-75"></div>

        <div
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform sm:w-full {{ $maxWidth }} sm:mx-auto">
            {{ $slot }}
        </div>
    </div>
@endif
