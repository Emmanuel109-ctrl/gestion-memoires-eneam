<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name', 'Mémoires ENEAM') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="h-full font-sans text-slate-900 antialiased">
        @php
            $utilisateur = auth()->user();
            $initiales = collect(explode(' ', trim($utilisateur->name)))
                ->filter()
                ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
                ->take(2)
                ->implode('');
            $couleurRole = match ($utilisateur->role) {
                'admin' => 'bg-indigo-500/15 text-indigo-200 ring-indigo-400/30',
                'bibliothecaire' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/30',
                default => 'bg-sky-500/15 text-sky-200 ring-sky-400/30',
            };
        @endphp

        <div x-data="{ ouvert: false }" class="min-h-full">
            {{-- Voile mobile --}}
            <div x-cloak x-show="ouvert" x-transition.opacity
                 class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
                 @click="ouvert = false"></div>

            {{-- Menu latéral --}}
            <aside x-cloak
                   :class="ouvert ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-slate-900 text-slate-300 transition-transform duration-200 lg:translate-x-0">

                <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500 text-sm font-bold text-white">M</span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold text-white">Mémoires ENEAM</span>
                        <span class="block text-[11px] uppercase tracking-[0.18em] text-slate-400">Bibliothèque</span>
                    </span>
                </div>

                <div class="border-b border-white/10 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-sm font-semibold text-white">
                            {{ $initiales ?: 'U' }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-white">{{ $utilisateur->name }}</p>
                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $couleurRole }}">
                                {{ $utilisateur->libelleRole() }}
                            </span>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5 text-sm">
                    <div>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            <x-nav-icon name="home" />
                            Tableau de bord
                        </a>
                    </div>

                    @if ($utilisateur->isAdmin())
                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Référentiels</p>
                            @if (Route::has('filieres.index'))
                                <a href="{{ route('filieres.index') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('filieres.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="folder" /> Filières
                                </a>
                            @endif
                            @if (Route::has('annees.index'))
                                <a href="{{ route('annees.index') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('annees.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="calendar" /> Années académiques
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($utilisateur->isAdmin() || $utilisateur->isBibliothecaire())
                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Bibliothèque</p>
                            @if (Route::has('memoires.index'))
                                <a href="{{ route('memoires.index') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('memoires.index', 'memoires.show') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="book" /> Mémoires
                                </a>
                            @endif
                            @if (Route::has('validations.index'))
                                <a href="{{ route('validations.index') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('validations.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="check" /> À valider
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($utilisateur->isAdmin())
                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Administration</p>
                            @if (Route::has('users.index'))
                                <a href="{{ route('users.index') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="users" /> Utilisateurs
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($utilisateur->isEtudiant())
                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Mes mémoires</p>
                            @if (Route::has('memoires.miennes'))
                                <a href="{{ route('memoires.miennes') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('memoires.miennes') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="book" /> Mes dépôts
                                </a>
                            @endif
                            @if (Route::has('memoires.create'))
                                <a href="{{ route('memoires.create') }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('memoires.create') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                    <x-nav-icon name="upload" /> Déposer un mémoire
                                </a>
                            @endif
                        </div>
                    @endif
                </nav>

                <div class="border-t border-white/10 px-3 py-4 text-sm">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('profile.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <x-nav-icon name="user" /> Mon profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left transition hover:bg-rose-500/10 hover:text-rose-200">
                            <x-nav-icon name="logout" /> Déconnexion
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Contenu --}}
            <div class="lg:pl-64">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="ouvert = true"
                                class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 lg:hidden">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                            </svg>
                        </button>

                        <div>
                            @isset($header)
                                {{ $header }}
                            @else
                                <h1 class="text-base font-semibold text-slate-900">{{ $title ?? 'Tableau de bord' }}</h1>
                            @endisset
                        </div>
                    </div>

                    <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 sm:inline-flex">
                        {{ $utilisateur->libelleRole() }}
                    </span>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            <x-nav-icon name="check" class="mt-0.5 h-5 w-5 text-emerald-500" />
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
