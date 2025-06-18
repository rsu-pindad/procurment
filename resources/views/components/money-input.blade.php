@props(['disabled' => false])

<div class="relative rounded-md shadow-sm">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <span class="text-gray-500 sm:text-sm">Rp</span>
    </div>

    <input type="text" inputmode="numeric"
        {{ $attributes->except('wire:model')->merge([
            'class' => 'block w-full pl-9 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm',
        ]) }}
        @disabled($disabled) x-data="{ raw: @entangle($attributes->wire('model')).defer }" x-init="$watch('raw', value => {
            let number = value.replace(/\D/g, '');
            $refs.input.value = new Intl.NumberFormat('id-ID').format(number);
        });"
        @input="raw = $event.target.value.replace(/\./g, '').replace(/\D/g, '')" x-ref="input">
</div>
