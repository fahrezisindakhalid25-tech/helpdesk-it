import './bootstrap';
import 'trix';
import 'trix/dist/trix.css';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(ChartDataLabels);
Chart.defaults.plugins.datalabels.display = true;
Chart.defaults.plugins.datalabels.color = '#ffffff';
Chart.defaults.plugins.datalabels.font = {
    weight: 'bold',
    size: 12,
};
window.Chart = Chart;
window.ChartDataLabels = ChartDataLabels; // Optional: for debugging
