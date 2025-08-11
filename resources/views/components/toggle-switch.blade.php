@props([
'id',
'name',
'type' => 'checkbox', // default ke checkbox
'label' => '',
'description' => '',
'checked' => false,
'value' => ''
])

@php
$isRadio = $type === 'radio';
@endphp

<div class="flex items-center justify-between gap-3">
    <label for="{{ $id }}" class="relative inline-flex items-center cursor-pointer">
        <!-- Hidden input -->
        <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" @checked($checked) class="sr-only peer" aria-labelledby="{{ $id }}-label" aria-describedby="{{ $id }}-description" {{ $attributes }} />

        <!-- Background track -->
        <div class="w-11 h-6 bg-gray-200 transition-colors duration-200 ease-in-out rounded-full ring-1 ring-inset ring-gray-900/5 peer-checked:bg-indigo-600"></div>

        <!-- Slider knob -->
        <div class="absolute left-0.5 top-0.5 h-5 w-5 bg-white rounded-full shadow-sm ring-1 ring-gray-900/5 transition-transform duration-200 ease-in-out
            @if($isRadio)
                peer-checked:translate-x-5
            @else
                peer-checked:translate-x-5
            @endif
        "></div>
    </label>

    <div class="text-sm">
        @if ($label)
        <label id="{{ $id }}-label" for="{{ $id }}" class="font-medium text-gray-900">{{ $label }}</label>
        @endif
        @if ($description)
        <span id="{{ $id }}-description" class="text-gray-500">{{ $description }}</span>
        @endif
    </div>
</div>
