<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net" rel="preconnect">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
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
                    console.log(`${eventName} event:`, e);
                });
            });
        </script>
        @stack('customScripts')
    </body>

</html>
