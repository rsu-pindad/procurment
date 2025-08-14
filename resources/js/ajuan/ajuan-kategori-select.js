import TomSelect from "tom-select";
import 'tom-select/dist/css/tom-select.css';
import 'tom-select/dist/js/plugins/virtual_scroll.js';
import 'tom-select/dist/js/plugins/optgroup_columns.js';

export function initTomSelect({ name, model, value = 'id', label = 'name' }) {
    const el = document.getElementById(`tomselect-${name}`);
    if (!el) return;

    if (el.tomselect) {
        el.tomselect.destroy();
    }

    const encodedModel = encodeURIComponent(model);
    const encodedValue = encodeURIComponent(value);
    const encodedLabel = encodeURIComponent(label);

    new TomSelect(el, {
        valueField: value,
        labelField: label,
        searchField: label,
        maxOptions: 20,
        plugins: ['virtual_scroll'],
        shouldLoad: () => true,
        firstUrl: (query) =>
            `/api/remote-select?model=${encodedModel}&value=${encodedValue}&label=${encodedLabel}&q=${encodeURIComponent(query)}`,
        load: (query, callback) => {
            fetch(`/api/remote-select?model=${encodedModel}&value=${encodedValue}&label=${encodedLabel}&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(callback)
                .catch(() => callback());
        },
        onFocus() {
            this.load('');
        },
        onChange(value) {
            Livewire.dispatch(`setSelected${name}`, { id: value });
        },
        onInitialize() {
            const defaultValue = el.dataset.selected;
            if (defaultValue) {
                this.setValue(defaultValue, true);
            }
        }
    });
}

export function registerTomSelectEvents({ name }) {
    console.log('register tom ' + name);

    const refresh = () => {
        const el = document.getElementById(`tomselect-${name}`);
        if (!el) return;

        const model = el.dataset.model;
        const value = el.dataset.value || 'id';
        const label = el.dataset.label || 'name';

        initTomSelect({ name, model, value, label });
    };

    Livewire.on(`refresh${name}Select`, () => {
        console.log('[TomSelect] Refresh triggered for kategori');
        // refresh();
        let select = document.getElementById(`tomselect-${name}`);
        let control = select.tomselect;
        control.enable();
    });

    Livewire.on(`reset${name}Select`, () => {
        console.log('[TomSelect] Reset triggered for kategori');

        let select = document.getElementById(`tomselect-${name}`);
        let control = select.tomselect;
        control.disable();
        // const el = document.getElementById(`tomselect-${name}`);
        // if (el?.tomselect) {
        //     el.tomselect.clear(true);
        //     const componentId = el.closest('[wire\\:id]')?.getAttribute('wire:id');
        //     const component = Livewire.find(componentId);
        //     component?.setSelected?.(null);
        // }
    });

    // ✅ Re-init setelah komponen kategori dirender ulang
    Livewire.hook('element.updated', (el, component) => {
        if (el.id === `tomselect-${name}`) {
            console.log('[TomSelect] Livewire.hook element.updated triggered');
            refresh();
        }
    });

    // Init pertama
    refresh();
}

