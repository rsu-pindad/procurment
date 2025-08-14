import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(...registerables, ChartDataLabels);

let jatuhTempoChartInstance = null;

export function renderJatuhTempoChart(labels, data) {
    setTimeout(() => {
        const canvas = document.getElementById('jatuhTempoChart');
        if (!canvas) {
            console.warn("Canvas belum tersedia saat render dipanggil.");
            return;
        }
        const ctx = canvas.getContext('2d');

        const chartData = {
            labels: labels,
            datasets: [{
                label: 'Jumlah Ajuan Jatuh Tempo',
                data: data,
                backgroundColor: '#f59e0b',
                borderColor: '#d97706',
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'center',
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value) => value.toLocaleString('id-ID')
                    }
                }
            },
            plugins: [ChartDataLabels]
        };

        if (jatuhTempoChartInstance) {
            jatuhTempoChartInstance.data.labels = labels;
            jatuhTempoChartInstance.data.datasets[0].data = data;
            jatuhTempoChartInstance.update();
        } else {
            jatuhTempoChartInstance = new Chart(ctx, config);
        }

        Livewire.dispatch('jatuhTempoChartRendered');
    }, 300);
}

export function initJatuhTempoChartEvents(initialLabels, initialData) {
    renderJatuhTempoChart(initialLabels, initialData);

    Livewire.on('refreshJatuhTempoChart', ({ labels, data }) => {
        renderJatuhTempoChart(labels, data);
    });

    const exportBtn = document.getElementById('export-chart-tempo-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.getElementById('jatuhTempoChart');
                if (!canvas) {
                    console.warn("tempo canvas belum ditemukan.");
                    return;
                }
                const base64Image = canvas.toDataURL('image/png');
                Livewire.dispatch('exportJatuhTempoChartPdf', {
                    image: base64Image
                });
            }, 300);
        });
    }
}
