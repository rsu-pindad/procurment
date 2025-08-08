@props(['label', 'value'])

<div {{ $attributes->merge(['class' => 'mt-4 flex w-full flex-none gap-x-4 px-6']) }}>
    <dt class="flex-none flex items-center">
        <span class="sr-only">{{ $label }}</span>
        {{ $slot }}
    </dt>
    <dd class="text-sm/6 text-gray-500">{{ $value }}</dd>
</div>
