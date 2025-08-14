import { Notyf } from 'notyf';

const notyf = new Notyf({
    duration: 10000,
    position: {
        x: 'center',
        y: 'center',
    },
    ripple: true,
    dismissible: true,
    types: [
        {
            type: 'info',
            background: 'blue',
            icon: false
        },
        {
            type: 'success',
            background: 'green',
            icon: false
        },
        {
            type: 'warning',
            background: 'orange',
            icon: false
        }
    ]
});

export default function initLivewireEventHandlers() {
    const infoHandlers = [
        {
            eventName: 'info-hapus',
            type: 'warning',
            message: (e) => e.message,
            dispatch: (e) => Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table'),
        },
        {
            eventName: 'unitNotificationReceived',
            type: 'info',
            dispatch: () => Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table'),
        },
        {
            eventName: 'modal-stored',
            type: 'success',
            message: () => 'Pengajuan berhasil dikirim.',
            dispatch: () => Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table'),
        },
        {
            eventName: 'modal-edited',
            type: 'info',
            message: () => 'Pengajuan berhasil diperbarui.',
            dispatch: () => Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table'),
        },
        {
            eventName: 'updated-status',
            type: 'info',
            message: () => 'Status berhasil dikonfirmasi.',
            dispatch: () => Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table'),
        },
        {
            eventName: 'updated-user-role',
            type: 'info',
            message: () => 'Role user berhasil dipilih.'
        },
        {
            eventName: 'updated-user-unit',
            type: 'info',
            message: () => 'Unit user berhasil dipilih.'
        },
    ];

    infoHandlers.forEach(({ eventName, message, dispatch, type }) => {
        Livewire.on(eventName, (event = {}) => {
            if (message) {
                notyf.open({
                    type: type || 'info',
                    message: typeof message === 'function' ? message(event) : message,
                });
            }

            if (typeof dispatch === 'function') {
                dispatch(event);
            }
        });
    });
}
