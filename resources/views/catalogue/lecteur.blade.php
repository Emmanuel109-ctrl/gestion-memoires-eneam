<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $memoire->titre }} — Lecture | {{ config('app.name', 'Mémoires ENEAM') }}</title>

        <link rel="icon" href="{{ asset('images/logo-eneam.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full overflow-hidden bg-slate-100 font-sans text-slate-900 antialiased">
        <header class="flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('catalogue.show', $memoire) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                   title="Revenir à la notice">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span class="hidden sm:inline">Notice</span>
                </a>

                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ $memoire->titre }}</p>
                    <p class="truncate text-[11px] text-slate-500">
                        {{ $memoire->auteur_nom ?? 'Auteur non renseigné' }}
                        @if ($memoire->filiere)
                            · {{ $memoire->filiere->nom }}
                        @endif
                        @if ($memoire->annee)
                            · {{ $memoire->annee->libelle }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('catalogue.lire', $memoire) }}" target="_blank" rel="noopener"
                   class="hidden items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 sm:inline-flex">
                    Ouvrir dans un onglet
                </a>

                @if ($peutTelecharger)
                    <a href="{{ route('catalogue.telecharger', $memoire) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-eneam-800 px-3 py-2 text-xs font-semibold text-white transition hover:bg-eneam-900">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span class="hidden sm:inline">Télécharger</span>
                    </a>
                @endif
            </div>
        </header>

        <main class="h-[calc(100vh-4rem)] w-full bg-slate-200">
            <iframe src="{{ route('catalogue.lire', $memoire) }}#view=FitH"
                    title="Lecture du mémoire : {{ $memoire->titre }}"
                    class="h-full w-full border-0"></iframe>
        </main>
    </body>
</html>
