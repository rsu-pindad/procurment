import Echo from 'laravel-echo';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';
import Pusher from 'pusher-js';

const notyf = new Notyf();
window.notyf = notyf;
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});
window.Echo.private(`user.${window.userId}`)
    .listen('NotificationReceived', (e) => {
        Livewire.dispatch('notificationReceived', e.message);
        notyf.success(e.message)
    });
