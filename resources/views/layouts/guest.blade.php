<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- استدعاء Tailwind CSS عبر CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- استدعاء jQuery عبر CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- استدعاء Axios عبر CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- <script type="module" src="{{ asset('js/app.js') }}"></script> --}}
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-start items-center sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">

                {{-- <img src="{{ asset('images/favicon.png') }}" width="200px" alt="Logo"> --}}
                <img class="pt-6" src="{{ asset('images/mv.png') }}" width="250px" alt="Logo">
                {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
            </a>
        </div>
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
    
    <!-- استدعاء Alpine.js عبر CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js"></script>
</body>

</html>
