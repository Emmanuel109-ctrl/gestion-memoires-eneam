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

            {{-- Panneau institutionnel --}}
            <aside class="relative hidden overflow-hidden bg-eneam-900 px-10 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-eneam-950 via-eneam-800 to-eneam-600"></div>
                <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-eneam-400/20 blur-3xl"></div>

                <div class="relative">
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

                <div class="relative max-w-xl py-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-eneam-200">École Nationale d'Économie Appliquée et Management</p>
                    <h1 class="mt-3 text-2xl font-semibold leading-tight tracking-tight text-white xl:text-[2rem]">
                        Le patrimoine scientifique de l'ENEAM, accessible en quelques clics.
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-eneam-100/90">
                        Consultez les mémoires soutenus, déposez votre travail et suivez chaque étape de sa validation.
                    </p>

                    {{-- Étudiants de l'ENEAM : uniforme officiel et cérémonie de rentrée --}}
                    <figure class="relative mt-5 max-w-[19rem]">
                        <div class="overflow-hidden rounded-2xl shadow-2xl shadow-eneam-950/50 ring-1 ring-white/25">
                            <img
                                src="{{ asset('images/etudiants-uniforme.jpg') }}"
                                alt="Étudiantes et étudiant de l'ENEAM en uniforme officiel"
                                width="1000"
                                height="707"
                                class="aspect-[10/7] w-full object-cover"
                            >
                        </div>
                        <div class="absolute -bottom-5 -right-2 h-20 w-28 overflow-hidden rounded-xl shadow-xl ring-4 ring-eneam-900">
                            <img
                                src="{{ asset('images/etudiants-amphi.jpg') }}"
                                alt="Étudiants de l'ENEAM réunis en amphithéâtre"
                                width="1000"
                                height="707"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <figcaption class="mt-3 pr-28 text-[11px] leading-snug text-eneam-200">
                            La communauté étudiante de l'ENEAM, sur le campus de Gbégamey.
                        </figcaption>
                    </figure>

                    <ul class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs text-eneam-100/90">
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-eneam-300"></span>
                            Dépôt sécurisé
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-eneam-300"></span>
                            Validation documentaire
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-eneam-300"></span>
                            Consultation encadrée
                        </li>
                    </ul>
                </div>

                <div class="relative border-t border-white/15 pt-6 text-xs text-eneam-200">
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
