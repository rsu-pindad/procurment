@props(['enum', 'model', 'name'])

@php
    $cases = collect($enum::cases())->filter(fn($case) => $case->value !== '')->all();
@endphp

<fieldset {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @foreach ($cases as $case)
        <label class="inline-flex items-center mr-6 cursor-pointer">
            <input class="form-radio text-indigo-600" name="{{ $name }}" type="radio" value="{{ $case->value }}"
                wire:model.live="{{ $model }}" />
            <span class="ml-2">{{ method_exists($case, 'label') ? $case->label() : $case->name }}</span>
        </label>
    @endforeach
</fieldset>
