<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Mémoires ENEAM') }}</title>
        <link rel="icon" href="{{ asset('images/logo-eneam.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">
            <aside class="relative hidden overflow-hidden bg-eneam-900 px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
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

                <div class="relative max-w-xl py-12">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-eneam-200">École Nationale d'Économie Appliquée et de Management</p>
                    <h1 class="mt-4 text-3xl font-semibold leading-tight tracking-tight text-white xl:text-4xl">
                        Le patrimoine scientifique de l'ENEAM, accessible en quelques clics.
                    </h1>
                    <p class="mt-4 text-sm leading-relaxed text-eneam-100/90">
                        Consultez les mémoires soutenus, déposez votre travail et suivez chaque étape de sa validation.
                        Un espace unique pour les étudiants, le secrétariat et l'équipe de la bibliothèque.
                    </p>
                </div>

                <div class="relative border-t border-white/15 pt-6 text-xs text-eneam-200">
                    <p class="font-medium text-eneam-100">03 BP 1079 — Gbégamey, Cotonou, Bénin</p>
                    <p class="mt-1">Bibliothèque numérique des mémoires · {{ date('Y') }}</p>
                </div>
            </aside>

            <main class="flex flex-col justify-center px-5 py-12 sm:px-8 lg:px-12">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <span class="flex h-12 w-16 items-center justify-center rounded-xl bg-white p-1.5 shadow-soft ring-1 ring-slate-200">
                            <img src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" class="h-full w-full object-contain">
                        </span>
                        <span class="leading-tight">
                            <span class="block text-sm font-semibold text-slate-900">Bibliothèque numérique</span>
                            <span class="block text-xs uppercase tracking-[0.16em] text-eneam-700">ENEAM</span>
                        </span>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200/80 sm:p-8">
                        <p class="eneam-eyebrow">Bienvenue</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Accéder à la bibliothèque</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">
                            Étudiants, connectez-vous avec votre matricule à 8 chiffres. Le personnel utilise son adresse e-mail professionnelle.
                        </p>

                        <div class="mt-6 space-y-3">
                            <a href="{{ route('login') }}"
                               class="flex w-full items-center justify-center gap-2 rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                                Se connecter
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                    Créer un compte étudiant
                                </a>
                            @endif
                        </div>
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
