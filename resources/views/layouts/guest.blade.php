<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Rental.ly') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans text-gray-900 antialiased relative overflow-x-hidden bg-slate-50">
        
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>

        <div class="min-h-screen flex flex-col justify-center items-center py-12 relative z-10">
            
            <div class="mb-6 drop-shadow-sm">
                <a href="/" class="text-4xl font-black text-blue-700 tracking-tight">Rental<span class="text-blue-400">.ly</span></a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-2xl sm:rounded-[2rem] border border-gray-100">
                {{ $slot }}
            </div>
            
        </div>
    </body>
</html>