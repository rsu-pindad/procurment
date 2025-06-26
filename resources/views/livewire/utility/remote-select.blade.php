<?php

use Livewire\Volt\Component;

new class extends Component {
    public $selected = null;

    public string $model; // e.g., App\Models\User
    public string $value = 'id';
    public string $label = 'name';
    public string $name = 'remoteSelect';

    public function mount($model, $value = 'id', $label = 'name', $name = 'remoteSelect')
    {
        $this->model = $model;
        $this->value = $value;
        $this->label = $label;
        $this->name = $name;
    }

    public function getSelectedProperty()
    {
        return $this->selected;
    }
}; ?>

<div>
    <x-customs.select-input-tom id="{{ $name }}" name="{{ $name }}" wire:model="selected">
        <option value="">{{ __('pilih/cari data...') }}</option>
    </x-customs.select-input-tom>
</div>

@push('customScripts')
    <script type="module">
        // document.addEventListener('open-modal', function(event) {
        // document.addEventListener('livewire:init', function(event) {
        // initTomeSelect();
        // if (event.detail === 'open-create-modal') {
        //     initTomeSelect();
        // }
        // });
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => {
                const el = document.querySelector('#{{ $name }}');
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
                    }
                });
            });
        }, 2000);

        // document.addEventListener('livewire:message.processed', () => {
        //     initTomeSelect();
        // });
        // document.addEventListener('modal-closed', function(event) {
        //     console.log('tom modal');
        //     tomSelectInstance.destroy();
        //     tomSelectInstance = null;
        // });
    </script>
@endpush
