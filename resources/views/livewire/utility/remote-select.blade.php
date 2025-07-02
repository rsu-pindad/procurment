<?php

use Livewire\Volt\Component;

new class extends Component {
    public $selected = null;

    public string $model; // e.g., App\Models\User
    public string $value = 'id';
    public string $label = 'name';
    public string $name = 'remoteSelect';

    public function mount($model, $value = 'id', $label = 'name', $name = 'remoteSelect', $selected = null)
    {
        $this->model = $model;
        $this->value = $value;
        $this->label = $label;
        $this->name = $name;
        $this->selected = $selected;
    }

    public function getSelectedProperty()
    {
        return $this->selected;
    }
}; ?>

<div wire:ignore>
    <x-customs.select-input-tom id="tomselect-{{ $name }}" name="{{ $name }}" wire:model="selected">
        <option value="">{{ __('pilih/cari data...') }}</option>
    </x-customs.select-input-tom>
</div>

@script
    <script type="module">
        document.addEventListener('livewire:navigated', () => {
            function initTomSelect() {
                const el = document.getElementById('tomselect-{{ $name }}');
                if (!el) return;
                // Hancurkan TomSelect sebelumnya jika sudah ada
                if (el.tomselect) {
                    el.tomselect.destroy();
                }

                new TomSelect(el, {
                    valueField: '{{ $value }}',
                    labelField: '{{ $label }}',
                    searchField: '{{ $label }}',
                    maxOptions: 20,
                    plugins: ['virtual_scroll'],
                    shouldLoad: function(query) {
                        return true;
                    },
                    firstUrl: function(query) {
                        return '/api/remote-select?model={{ urlencode($model) }}&value={{ $value }}&label={{ $label }}&q=' +
                            encodeURIComponent(query);
                    },
                    load(query, callback) {
                        fetch(`/api/remote-select?model={{ urlencode($model) }}&value={{ $value }}&label={{ $label }}&q=` +
                                encodeURIComponent(query))
                            .then(res => res.json())
                            .then(callback)
                            .catch(() => callback());
                    },
                    onFocus() {
                        this.load('');
                    },
                    onChange(value) {
                        Livewire.dispatch('setSelected{{ $name }}', {
                            id: value
                        });
                    },
                    onInitialize() {
                        const defaultValue = "{{ $selected }}";
                        if (defaultValue) {
                            this.setValue(defaultValue, true);
                        }
                    }
                });
            }
            initTomSelect();
        });
    </script>
@endscript
