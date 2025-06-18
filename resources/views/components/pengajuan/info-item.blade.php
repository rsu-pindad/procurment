@props(['label', 'icon' => null, 'value'])

<div {{ $attributes->merge(['class' => 'mt-4 flex w-full flex-none gap-x-4 px-6']) }}>
    <dt class="flex-none">
        <span class="sr-only">{{ $label }}</span>
        @if ($icon)
            {!! $icon !!}
        @endif
    </dt>
    <dd class="text-sm/6 text-gray-500">{{ $value }}</dd>
</div>
