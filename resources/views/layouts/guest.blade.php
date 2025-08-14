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

<body data-user-id="{{ auth()->id() }}"  data-page="{{ Route::currentRouteName() }}" class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="flex flex-col items-center w-full sm:max-w-md">
            <div class="mb-6">
                <a href="#" wire:navigate>
                    <x-application-logo class="fill-current w-24 h-24 md:w-28 md:h-28 text-gray-500" />
                </a>
            </div>
            <div class="w-full px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>


</html>
