import './bootstrap';

import'./../../vendor/power-components/livewire-powergrid/dist/tailwind.css';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);
dayjs.locale('id');
window.dayjs = dayjs;

import TomSelect from "tom-select";
import 'tom-select/dist/css/tom-select.css';
window.TomSelect = TomSelect
import 'tom-select/dist/js/plugins/virtual_scroll.js';
import 'tom-select/dist/js/plugins/optgroup_columns.js';

import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

window.Chart = Chart;
