import initLivewireEventHandlers from "./ajuan-handler";
import { registerTomSelectEvents } from "./ajuan-kategori-select";

export default function initAjuan() {
    const run = () => {
        registerTomSelectEvents({ name: 'kategori' });
        initLivewireEventHandlers();
    };
    run();
    document.addEventListener('livewire:init', run);
}
