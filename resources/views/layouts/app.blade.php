<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @filamentStyles
</head>

<body class='bg-[#F3F4F6] dark:bg-gray-900'>
    <div
        class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/30 dark:bg-blue-600/20 rounded-full blur-[100px] animate-pulse">
    </div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-400/30 dark:bg-indigo-600/20 rounded-full blur-[100px] animate-pulse"
        style="animation-delay: 2s;"></div>

    {{ $slot }}

    @livewireScripts
    @filamentScripts
</body>

</html>
