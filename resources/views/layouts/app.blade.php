<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f4f6fb]">
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
    <body class="h-full font-sans text-slate-900 antialiased">
        @php
            $utilisateur = auth()->user();
            $initiales = collect(explode(' ', trim($utilisateur->name)))
                ->filter()
                ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
                ->take(2)
                ->implode('');
            $couleurRole = match ($utilisateur->role) {
                'admin' => 'bg-eneam-50 text-eneam-800 ring-eneam-200',
                'bibliothecaire' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                default => 'bg-sky-50 text-sky-700 ring-sky-200',
            };
            $nonLues = Route::has('notifications.index')
                ? $utilisateur->unreadNotifications()->count()
                : 0;
        @endphp

        <div x-data="{ ouvert: false }" class="min-h-full">
            {{-- Voile mobile --}}
            <div x-cloak x-show="ouvert" x-transition.opacity
                 class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
                 @click="ouvert = false"></div>

            {{-- Menu latéral --}}
            <aside x-cloak
                   :class="ouvert ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0">

                <a href="{{ route('dashboard') }}" class="flex h-20 items-center gap-3 border-b border-slate-100 px-5">
                    <span class="flex h-12 w-16 items-center justify-center rounded-xl bg-white p-1 ring-1 ring-slate-200">
                        <img src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" class="h-full w-full object-contain">
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold tracking-tight text-slate-900">Bibliothèque ENEAM</span>
                        <span class="mt-0.5 block text-[11px] font-medium uppercase tracking-[0.16em] text-eneam-700">Mémoires</span>
                    </span>
                </a>

                <div class="border-b border-slate-100 px-4 py-4">
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-100">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-eneam-800 text-sm font-semibold text-white">
                            {{ $initiales ?: 'U' }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $utilisateur->name }}</p>
                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset {{ $couleurRole }}">
                                {{ $utilisateur->libelleRole() }}
                            </span>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5 text-sm">
                    <div>
                        <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Pilotage</p>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-nav-icon name="home" />
                            Tableau de bord
                        </a>
                    </div>

                    <div>
                        <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Ma sélection</p>
                        @if (Route::has('favoris.index'))
                            <a href="{{ route('favoris.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('favoris.*') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-nav-icon name="star" /> Mes favoris
                            </a>
                        @endif
                        @if (Route::has('notifications.index'))
                            <a href="{{ route('notifications.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('notifications.*') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-nav-icon name="bell" /> Notifications
                                @if ($nonLues > 0)
                                    <span class="ml-auto inline-flex min-w-5 items-center justify-center rounded-full bg-rose-100 px-1.5 py-0.5 text-[10px] font-semibold text-rose-700">
                                        {{ $nonLues > 9 ? '9+' : $nonLues }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    </div>

                    @if ($utilisateur->isAdmin())
                        <div>
                            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Référentiels</p>
                            @if (Route::has('filieres.index'))
                                <a href="{{ route('filieres.index') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('filieres.*') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="folder" /> Filières
                                </a>
                            @endif
                            @if (Route::has('annees.index'))
                                <a href="{{ route('annees.index') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('annees.*') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="calendar" /> Années académiques
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($utilisateur->isAdmin() || $utilisateur->isBibliothecaire())
                        <div>
                            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Bibliothèque</p>
                            @if (Route::has('memoires.index'))
                                <a href="{{ route('memoires.index') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('memoires.index', 'memoires.show') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="book" /> Mémoires
                                </a>
                            @endif
                            @if (Route::has('validations.index'))
                                <a href="{{ route('validations.index') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('validations.*') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="check" /> Vérification documentaire
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($utilisateur->isAdmin())
                        <div>
                            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Administration</p>
                            @if (Route::has('users.index'))
                                <a href="{{ route('users.index') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('users.*') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="users" /> Comptes du personnel
                                </a>
                            @endif
                            @if (Route::has('validations.historique'))
                                <a href="{{ route('validations.historique') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('validations.historique') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="check" /> Historique des décisions
                                </a>
                            @endif
                        </div>
                    @endif

                    @if ($utilisateur->isEtudiant())
                        <div>
                            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Mon espace</p>
                            @if (Route::has('memoires.miennes'))
                                <a href="{{ route('memoires.miennes') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('memoires.miennes') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="book" /> Mes mémoires
                                </a>
                            @endif
                            @if (Route::has('memoires.create'))
                                <a href="{{ route('memoires.create') }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('memoires.create') ? 'bg-eneam-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <x-nav-icon name="upload" /> Déposer un mémoire
                                </a>
                            @endif
                        </div>
                    @endif
                </nav>

                <div class="border-t border-slate-100 px-3 py-4 text-sm">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-nav-icon name="user" /> Mon profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left font-medium text-slate-500 transition hover:bg-rose-50 hover:text-rose-700">
                            <x-nav-icon name="logout" /> Déconnexion
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Contenu --}}
            <div class="lg:pl-72">
                <header class="sticky top-0 z-30 flex h-20 items-center justify-between gap-4 border-b border-slate-200/80 bg-white/85 px-4 backdrop-blur-xl sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="ouvert = true"
                                class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden">
                            <span class="sr-only">Ouvrir le menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            @isset($header)
                                {{ $header }}
                            @else
                                <h1 class="truncate text-base font-semibold text-slate-900">{{ $title ?? 'Tableau de bord' }}</h1>
                            @endisset
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('notifications.index'))
                            <a href="{{ route('notifications.index') }}"
                               class="relative rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                               title="Notifications">
                                <span class="sr-only">Notifications</span>
                                <x-nav-icon name="bell" class="h-5 w-5" />
                                @if ($nonLues > 0)
                                    <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold text-white">
                                        {{ $nonLues > 9 ? '9+' : $nonLues }}
                                    </span>
                                @endif
                            </a>
                        @endif
                        <span class="hidden items-center gap-2 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200 sm:inline-flex">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            {{ $utilisateur->libelleRole() }}
                        </span>
                        <a href="{{ route('profile.edit') }}"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-eneam-800 text-xs font-semibold text-white ring-2 ring-white"
                           title="Mon profil">
                            {{ $initiales ?: 'U' }}
                        </a>
                    </div>
                </header>

                <main class="mx-auto max-w-[1400px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @if (session('success'))
                        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            <x-nav-icon name="check" class="mt-0.5 h-5 w-5 text-emerald-500" />
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            <span class="mt-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-[11px] font-bold">!</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                <footer class="mx-auto max-w-[1400px] px-4 pb-8 text-xs text-slate-400 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-2 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p>École Nationale d'Économie Appliquée et de Management · Bibliothèque numérique</p>
                        <p>© {{ date('Y') }} ENEAM — Tous droits réservés</p>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
