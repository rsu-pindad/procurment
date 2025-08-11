<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net" rel="preconnect">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <livewire:layout.navigation />
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif
        <main>
            {{ $slot }}
        </main>
        <livewire:notification-manager />
    </div>
    <script type="module">
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
        window.userId = @js(auth()->id());
        const userChannel = Echo.private(`user.${window.userId}`);
        const notificationHandlers = [{
                eventName: 'NotificationReceived',
                livewireEvent: 'notificationReceived',
            },
            {
                eventName: 'UnitNotificationReceived',
                livewireEvent: 'unitNotificationReceived',
            }
        ];
        notificationHandlers.forEach(({
            eventName,
            livewireEvent
        }) => {
            userChannel.listen(eventName, (e) => {
                notyf.open({
                    type: 'info',
                    message: e.message
                });
                Livewire.dispatch(livewireEvent, e);
                // console.log(`${eventName} event:`, e);
            });
        });
    </script>
    <script type="module">
        const notyf = new Notyf({
            duration: 10000,
            position: {
                x: 'center',
                y: 'center',
            },
            ripple: true,
            dismissible: true,
            types: [{
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

        const infoHandlers = [{
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

        infoHandlers.forEach(({
            eventName,
            message,
            dispatch,
            type
        }) => {
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
    </script>

    @stack('customScripts')
</body>

</html>
