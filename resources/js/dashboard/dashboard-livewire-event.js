import { initHpsChartEvents } from './chart-hps';
import { initStatusChartEvents } from './chart-status';
import { initJatuhTempoChartEvents } from './chart-tempo';

export default function initDashboard() {
    const run = () => {
        // HPS Chart
        const hpsLabels = JSON.parse(document.getElementById('hpsChartLabels')?.textContent || '[]');
        const hpsData = JSON.parse(document.getElementById('hpsChartData')?.textContent || '[]');
        // console.log(hpsData);
        initHpsChartEvents(hpsLabels, hpsData);

        // Status Chart
        const statusLabels = JSON.parse(document.getElementById('statusChartLabels')?.textContent || '[]');
        const statusData = JSON.parse(document.getElementById('statusChartData')?.textContent || '[]');
        initStatusChartEvents(statusLabels, statusData);

        // Jatuh Tempo Chart
        const tempoLabels = JSON.parse(document.getElementById('jatuhTempoChartLabels')?.textContent || '[]');
        const tempoData = JSON.parse(document.getElementById('jatuhTempoChartData')?.textContent || '[]');
        initJatuhTempoChartEvents(tempoLabels, tempoData);
    };

    // Jalankan langsung (untuk kasus JS dimuat setelah Livewire)
    run();

    // Jalankan lagi kalau Livewire inisialisasi ulang (misalnya lewat Turbolinks/SPA)
    document.addEventListener('livewire:init', run);
}

