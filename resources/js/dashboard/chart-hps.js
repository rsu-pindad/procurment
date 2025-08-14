// import { Chart, registerables } from 'chart.js';
// import ChartDataLabels from 'chartjs-plugin-datalabels';

// Chart.register(...registerables, ChartDataLabels);

import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend, ChartDataLabels);

let hpsChartInstance = null;

function formatShortNumber(value) {
    if (value >= 1_000_000_000) {
        return (value / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + ' B';
    }
    if (value >= 1_000_000) {
        return (value / 1_000_000).toFixed(1).replace(/\.0$/, '') + ' M';
    }
    if (value >= 1_000) {
        return (value / 1_000).toFixed(0) + ' K';
    }
    return value.toLocaleString('id-ID');
}

export function renderHpsChart(labels, data) {
    setTimeout(() => {
        const canvas = document.getElementById('hpsChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const chartData = {
            labels: labels,
            datasets: [
                {
                    label: 'Total HPS',
                    data: data.hps,
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                },
                {
                    label: 'Total HPS Nego',
                    data: data.hps_nego,
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                }
            ]
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
                            callback: value => formatShortNumber(value)
                        }
                    }
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#333',
                        font: { weight: 'bold' },
                        formatter: value => formatShortNumber(value)
                    }
                }
            },
            plugins: [ChartDataLabels]
        };

        if (hpsChartInstance) {
            hpsChartInstance.data.labels = labels;
            hpsChartInstance.data.datasets[0].data = data.hps;
            hpsChartInstance.data.datasets[1].data = data.hps_nego;
            hpsChartInstance.update();
        } else {
            hpsChartInstance = new Chart(ctx, config);
        }
    }, 300);
}

export function initHpsChartEvents(initialLabels, initialData) {
    renderHpsChart(initialLabels, initialData);

    Livewire.on('refreshHpsChart', ({ labels, data }) => {
        renderHpsChart(labels, data);
    });

    const exportBtn = document.getElementById('export-chart-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.getElementById('hpsChart');
                if (!canvas) {
                    console.warn("hps canvas belum ditemukan.");
                    return;
                }
                const base64Image = canvas.toDataURL('image/png');
                Livewire.dispatch('exportHpsChartPdf', {
                    image: base64Image
                });
            }, 300);
        });
    }
}
