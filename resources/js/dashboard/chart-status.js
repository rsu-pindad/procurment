import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(...registerables, ChartDataLabels);

let statusChartInstance = null;

export function renderStatusChart(labels, data) {
    setTimeout(() => {
        const ctx = document.getElementById('statusChart')?.getContext('2d');
        if (!ctx) return;

        const chartData = {
            labels: labels,
            datasets: [{
                label: 'Jumlah Ajuan per Status',
                data: data,
                backgroundColor: [
                    '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
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
                            precision: 0,
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Distribusi Ajuan berdasarkan Status'
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'center',
                        color: '#333',
                        font: { weight: 'bold' },
                        formatter: (value) => value.toLocaleString('id-ID')
                    }
                },
            },
            plugins: [ChartDataLabels]
        };

        if (statusChartInstance) {
            statusChartInstance.data.labels = labels;
            statusChartInstance.data.datasets[0].data = data;
            statusChartInstance.update();
        } else {
            statusChartInstance = new Chart(ctx, config);
        }
    }, 300);
}

export function initStatusChartEvents(initialLabels, initialData) {
    renderStatusChart(initialLabels, initialData);

    Livewire.on('refreshStatusChart', ({ labels, data }) => {
        renderStatusChart(labels, data);
    });

    const exportBtn = document.getElementById('export-chart-status-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            setTimeout(() => {
                const canvas = document.getElementById('statusChart');
                if (!canvas) {
                    console.warn("status canvas belum ditemukan.");
                    return;
                }
                const base64Image = canvas.toDataURL('image/png');
                Livewire.dispatch('exportStatusChartPdf', {
                    image: base64Image
                });
            }, 300);
        });
    }
}
