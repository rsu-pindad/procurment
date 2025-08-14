<?php

use Livewire\Volt\Component;

new class extends Component
{
    public $selected = null;

    public string $model;
    public string $value = 'id';
    public string $label = 'name';
    public string $name = 'remoteSelect';
    public bool $disabled = false;

    public function mount($model, $value = 'id', $label = 'name', $name = 'remoteSelect', $selected = null, $disabled = false)
    {
        $this->model = $model;
        $this->value = $value;
        $this->label = $label;
        $this->name = $name;
        $this->selected = $selected;
        $this->disabled = $disabled;
    }

    public function getSelectedProperty()
    {
        return $this->selected;
    }

    public function setSelected($value)
    {
        $this->selected = $value;
    }
};
?>

<div wire:ignore>
    <x-customs.select-input-tom id="tomselect-{{ $name }}" name="{{ $name }}" wire:model="selected" data-selected="{{ $selected }}" data-model="{{ $model }}" data-value="{{ $value }}" data-label="{{ $label }}" :disabled="$disabled">
        <option value="">{{ __('pilih/cari data...') }}</option>
    </x-customs.select-input-tom>
</div>
