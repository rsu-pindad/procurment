@props(['disabled' => false])

<div wire:ignore>
    <select {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'tom-select border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full']) }}>
        {{ $slot }}
    </select>
</div>
