<?php

use Livewire\Volt\Component;

new class extends Component {
    public ?string $selected = null;
    public string $model;
    public string $value = 'id';
    public string $label = 'name';
    public string $name = 'dependentSelect';
    public array $optgroups = [];

    public function mount($model, $value = 'id', $label = 'name', $name = 'dependentSelect')
    {
        $this->model = $model;
        $this->value = $value;
        $this->label = $label;
        $this->name = $name;
    }
    public function getSelectedProperty(): ?string
    {
        return $this->selected;
    }
};
?>
<div>
    <input
        class="tom-select border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
        id="{{ $name }}" name="{{ $name }}" type="text" wire:model="selected"
        placeholder="{{ __('pilih/cari data...') }}" x-ref="select" />
</div>

@pushOnce('customScripts')
    <script type="module">
        document.addEventListener('livewire:init', function() {
            new TomSelect('#{{ $name }}', {
                valueField: '{{ $value }}',
                labelField: '{{ $label }}',
                searchField: '{{ $label }}',
                maxOptions: 20,
                plugins: ['optgroup_columns'],
                shouldLoad: function(query) {
                    return true; // selalu load
                },
                load(query, callback) {
                    fetch(`/api/depend-remote-select?model={{ urlencode($model) }}&value={{ $value }}&label={{ $label }}&q=` +
                            encodeURIComponent(query))
                        .then(res => res.json())
                        .then(callback)
                        .catch(() => callback());
                },
                onFocus() {
                    this.load('');
                },
                // onChange(value) {
                //     console.log('child value komponent adalah ' + value);
                //     @this.set('selected', value);
                // },
                // onChange: function(value) {
                onChange(value) {
                    Livewire.dispatch('setSelected' + '{{ $name }}', {
                        id: value
                    });
                }
            });
        });
    </script>
@endPushOnce
