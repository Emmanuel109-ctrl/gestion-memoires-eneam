<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name', 'Mémoires ENEAM') }}</title>

        <link rel="icon" href="{{ asset('images/logo-eneam.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

        <div class="grid min-h-screen lg:h-screen lg:grid-cols-[1.05fr_1fr]">

            {{-- Panneau institutionnel : texte en haut, photo animée en bandeau bas --}}
            <aside class="relative hidden overflow-hidden bg-eneam-950 lg:flex lg:flex-col">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-eneam-950 via-eneam-900 to-eneam-800"></div>

                <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-2xl"></div>

                <div class="relative px-10 py-10 xl:px-16">
                    <a href="/" class="inline-flex items-center gap-4">
                        <span class="flex h-16 w-20 items-center justify-center rounded-2xl bg-white p-2 shadow-lg shadow-eneam-950/30">
                            <img src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" class="h-full w-full object-contain">
                        </span>
                        <span class="leading-tight">
                            <span class="block text-sm font-semibold uppercase tracking-[0.18em] text-eneam-100">ENEAM</span>
                            <span class="block text-base font-semibold text-white">Bibliothèque numérique</span>
                            <span class="block text-xs text-eneam-200">Mémoires &amp; travaux de fin d'études</span>
                        </span>
                    </a>
                </div>

                <div class="relative max-w-lg px-10 py-6 xl:px-16">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-eneam-200">École Nationale d'Économie Appliquée et Management</p>
                    <h1 class="mt-3 text-2xl font-semibold leading-[1.2] tracking-tight text-white">
                        Le patrimoine scientifique de l'ENEAM, accessible en quelques clics.
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-eneam-100/90">
                        Consultez les mémoires soutenus, déposez votre travail et suivez chaque étape de sa validation.
                    </p>

                    <ul class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs text-eneam-100/90">
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-eneam-300"></span>
                            Dépôt sécurisé
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-eneam-300"></span>
                            Validation
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-eneam-300"></span>
                            Consultation encadrée
                        </li>
                    </ul>
                </div>

                {{-- Grande photo des étudiantes, animée comme le hero du site ENEAM --}}
                <div class="relative mt-2 min-h-0 flex-1 overflow-hidden">
                    <img
                        src="{{ asset('images/etudiants-uniforme.jpg') }}"
                        alt=""
                        aria-hidden="true"
                        class="animate-eneam-kenburns absolute inset-0 h-full w-full object-cover object-[center_30%]"
                    >
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-eneam-950/80 via-transparent to-eneam-950/30"></div>
                </div>

                <div class="relative z-10 mt-auto border-t border-white/15 bg-eneam-950/95 px-10 py-6 text-xs text-eneam-200 xl:px-16">
                    <p class="font-medium text-eneam-100">03 BP 1079 — Gbégamey, Cotonou, Bénin</p>
                    <p class="mt-1">Bibliothèque numérique des mémoires · {{ date('Y') }}</p>
                </div>
            </aside>

            {{-- Formulaire --}}
            <main class="flex flex-col justify-center px-5 py-10 sm:px-8 lg:max-h-screen lg:overflow-y-auto lg:px-12 lg:[justify-content:safe_center]">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <span class="flex h-12 w-16 items-center justify-center rounded-xl bg-white p-1.5 shadow-soft ring-1 ring-slate-200/70">
                            <img src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" class="h-full w-full object-contain">
                        </span>
                        <span class="leading-tight">
                            <span class="block text-sm font-semibold text-slate-900">Bibliothèque numérique</span>
                            <span class="block text-xs uppercase tracking-[0.16em] text-eneam-700">ENEAM</span>
                        </span>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200/80 sm:p-8">
                        {{ $slot }}
                    </div>

                    <div class="mt-8 flex items-center justify-between gap-4 text-xs text-slate-500">
                        <span>© {{ date('Y') }} ENEAM</span>
                        <a href="https://eneam.uac.bj/" target="_blank" rel="noopener" class="font-medium text-eneam-700 hover:text-eneam-900">
                            Site officiel ↗
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
