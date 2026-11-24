<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mémoires ENEAM') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-100">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
            <a href="/" class="flex items-center gap-3 mb-8">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-sm">
                    M
                </span>
                <span class="text-left">
                    <span class="block text-lg font-semibold leading-tight">Bibliothèque des mémoires</span>
                    <span class="block text-xs uppercase tracking-[0.2em] text-slate-500">ENEAM</span>
                </span>
            </a>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} ENEAM — Gestion et lecture des mémoires
            </p>
        </div>
    </body>
</html>
