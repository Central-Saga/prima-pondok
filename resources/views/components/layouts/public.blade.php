<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ \App\Models\Setting::get('site_name', 'Pondok Teges') }}</title>
        @vite('resources/css/app.css')
        @vite('resources/js/app.js')
    </head>
    <body class="min-h-screen bg-white text-slate-800">
        @include('partials.site-header')

        {{ $slot }}

        @include('partials.site-footer')
    </body>
</html>
