@props(['disabled' => false])

<textarea
    {{ $attributes->merge([
        'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full',
        'rows' => 4,
    ]) }}
    @disabled($disabled)></textarea>
