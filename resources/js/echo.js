import Echo from '@ably/laravel-echo';
import * as Ably from 'ably';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.Notyf = Notyf;
const notyf = new Notyf({
    duration: 10000,
    position: {
        x: 'right',
        y: 'top',
    },
    ripple: true,
    dismissible: true,
    types: [
        {
            type: 'info',
            background: 'blue',
            icon: false
        }
    ]
});

window.Ably = Ably;
window.Echo = new Echo({
    broadcaster: 'ably',
    tls: true,
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});

window.Echo.connector.ably.connection.on(stateChange => {
    if (stateChange.current === 'connected') {
        console.log('connected to ably server');
    }
});

window.Echo.private(`user.${window.userId}`)
    .listen('NotificationReceived', (e) => {
        notyf.open({
            type: 'info',
            message: e.message
        });
        Livewire.dispatch('notificationReceived', e);
        console.log('NotificationReceived event:', e);
    });

