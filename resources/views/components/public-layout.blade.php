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
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="min-h-screen bg-[#f4f6fb] font-sans text-slate-900 antialiased">
        <div x-data="{ ouvert: false }" class="min-h-screen">
            <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
                <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                        <span class="flex h-12 w-16 shrink-0 items-center justify-center rounded-xl bg-white p-1 ring-1 ring-slate-200">
                            <img src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" class="h-full w-full object-contain">
                        </span>
                        <span class="min-w-0 leading-tight">
                            <span class="block text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">ENEAM</span>
                            <span class="block truncate text-sm font-semibold text-slate-900">Bibliothèque numérique</span>
                            <span class="hidden text-[11px] text-slate-500 sm:block">Mémoires de soutenance</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-1 md:flex">
                        <a href="{{ route('home') }}"
                           class="whitespace-nowrap rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('home') ? 'bg-eneam-50 text-eneam-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Accueil
                        </a>
                        <a href="{{ route('catalogue.index') }}"
                           class="whitespace-nowrap rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs('catalogue.*') ? 'bg-eneam-50 text-eneam-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Catalogue
                        </a>

                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="whitespace-nowrap rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                Mon espace
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="ml-2 inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                                Tableau de bord
                            </a>
                        @else
                            <a href="{{ route('activation.create') }}"
                               class="hidden whitespace-nowrap rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 lg:inline-block">
                                Première connexion
                            </a>
                            <a href="{{ route('login') }}"
                               class="ml-2 inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                                Se connecter
                            </a>
                        @endauth
                    </nav>

                    <button type="button" @click="ouvert = !ouvert"
                            class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 md:hidden">
                        <span class="sr-only">Ouvrir le menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>
                </div>

                <div x-cloak x-show="ouvert" x-transition.opacity class="border-t border-slate-100 bg-white px-4 py-4 md:hidden">
                    <div class="mx-auto flex max-w-7xl flex-col gap-1 text-sm">
                        <a href="{{ route('home') }}" class="rounded-xl px-3 py-2.5 font-medium text-slate-700 hover:bg-slate-100">Accueil</a>
                        <a href="{{ route('catalogue.index') }}" class="rounded-xl px-3 py-2.5 font-medium text-slate-700 hover:bg-slate-100">Catalogue</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-xl px-3 py-2.5 font-medium text-slate-700 hover:bg-slate-100">Mon espace</a>
                        @else
                            <a href="{{ route('activation.create') }}" class="rounded-xl px-3 py-2.5 font-medium text-slate-700 hover:bg-slate-100">Première connexion</a>
                            <a href="{{ route('login') }}" class="mt-1 rounded-xl bg-eneam-800 px-3 py-2.5 text-center font-semibold text-white">Se connecter</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>

            <footer class="mt-16 border-t border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-14 items-center justify-center rounded-lg bg-white p-1 ring-1 ring-slate-200">
                                <img src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" class="h-full w-full object-contain">
                            </span>
                            <div class="leading-tight">
                                <p class="text-sm font-semibold text-slate-900">Bibliothèque numérique</p>
                                <p class="text-xs uppercase tracking-[0.14em] text-eneam-700">ENEAM</p>
                            </div>
                        </div>
                        <p class="mt-4 max-w-sm text-xs leading-relaxed text-slate-500">
                            École Nationale d'Économie Appliquée et de Management.
                            Conservation et diffusion encadrée des mémoires de soutenance.
                        </p>
                    </div>

                    <div class="text-sm">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Accès rapide</p>
                        <ul class="mt-3 space-y-2 text-slate-600">
                            <li><a href="{{ route('catalogue.index') }}" class="hover:text-eneam-800">Catalogue des mémoires</a></li>
                            <li><a href="{{ route('login') }}" class="hover:text-eneam-800">Connexion étudiant / personnel</a></li>
                            <li><a href="{{ route('activation.create') }}" class="hover:text-eneam-800">Première connexion étudiante</a></li>
                        </ul>
                    </div>

                    <div class="text-xs leading-relaxed text-slate-500">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Établissement</p>
                        <p class="mt-3 font-medium text-slate-700">03 BP 1079 — Gbégamey, Cotonou, Bénin</p>
                        <p class="mt-1">Université d'Abomey-Calavi</p>
                        <a href="https://eneam.uac.bj/" target="_blank" rel="noopener" class="mt-3 inline-block font-semibold text-eneam-700 hover:text-eneam-900">Site officiel ↗</a>
                    </div>
                </div>
                <div class="border-t border-slate-100">
                    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>© {{ date('Y') }} ENEAM — Bibliothèque numérique des mémoires</p>
                        <p>Accès réservé aux étudiants et au personnel autorisés pour le texte intégral.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
