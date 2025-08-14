import { Notyf } from 'notyf';

const notyf = new Notyf({
    duration: 10000,
    position: {
        x: 'center',
        y: 'top',
    },
    ripple: true,
    dismissible: true,
    types: [{
        type: 'info',
        background: 'blue',
        icon: false
    }]
});

function initNotificationListener(userId) {
    console.log('modul notifikasi aktif');
    const userChannel = Echo.private(`user.${userId}`);
    const notificationHandlers = [
        { eventName: 'NotificationReceived', livewireEvent: 'notificationReceived' },
        { eventName: 'UnitNotificationReceived', livewireEvent: 'unitNotificationReceived' }
    ];

    notificationHandlers.forEach(({ eventName, livewireEvent }) => {
        userChannel.listen(eventName, (e) => {
            notyf.open({
                type: 'info',
                message: e.message
            });
            console.log(e.message);
            Livewire.dispatch(livewireEvent, e);
        });
    });
}

window.initNotificationListener = initNotificationListener;

export default initNotificationListener;
