import './bootstrap';

import'./../../vendor/power-components/livewire-powergrid/dist/tailwind.css';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);
dayjs.locale('id');
window.dayjs = dayjs;
