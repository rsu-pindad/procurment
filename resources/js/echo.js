import Echo from '@ably/laravel-echo';
import * as Ably from 'ably';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.Notyf = Notyf;
// const notyf = new Notyf({
//     duration: 10000,
//     position: {
//         x: 'right',
//         y: 'top',
//     },
//     ripple: true,
//     dismissible: true,
//     types: [
//         {
//             type: 'info',
//             background: 'blue',
//             icon: false
//         }
//     ]
// });

window.Ably = Ably;
window.Echo = new Echo({
    broadcaster: 'ably',
    // key: import.meta.env.VITE_ABLY_PUBLIC_KEY,
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
    } else if (stateChange.current === 'connecting') {
        console.log('connecting to ably');
    } else {
        console.log(stateChange);
    }
});
// window.Echo.connector.ably.connection.on('stateChange', (stateChange) => {
//     if (stateChange.current === 'connected') {
//         console.log('Connected to Ably server');
//     } else if (stateChange.current === 'failed') {
//         console.error('Failed to connect to Ably server:', stateChange.reason);
//     }
// });
// window.Echo.private(`user.${window.userId}`)
//     .listen('NotificationReceived', (e) => {
//         notyf.open({
//             type: 'info',
//             message: e.message
//         });
//         Livewire.dispatch('notificationReceived', e);
//         console.log('NotificationReceived event:', e);
//     });

