<x-public-layout title="Accueil">
    {{-- Hero institutionnel --}}
    <section class="relative overflow-hidden bg-eneam-950 text-white">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-eneam-950 via-eneam-900 to-eneam-700"></div>
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-20 h-[28rem] w-[28rem] rounded-full bg-eneam-400/20 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.15fr_0.85fr] lg:px-8 lg:py-20">
            <div class="max-w-2xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-eneam-200">
                    École Nationale d'Économie Appliquée et de Management
                </p>
                <h1 class="mt-5 text-4xl font-semibold leading-[1.1] tracking-tight sm:text-5xl">
                    Le patrimoine scientifique de l'ENEAM, accessible de façon encadrée.
                </h1>
                <p class="mt-5 max-w-xl text-sm leading-relaxed text-eneam-100/90 sm:text-base">
                    Consultez les notices et les pages de garde des mémoires de soutenance.
                    Les étudiants ENEAM authentifiés accèdent au texte intégral selon les règles
                    de confidentialité et d'embargo.
                </p>

                <form method="GET" action="{{ route('catalogue.index') }}" class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <label for="q" class="sr-only">Rechercher un mémoire</label>
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                        </svg>
                        <input id="q" name="q" type="search"
                               placeholder="Titre, auteur, mot-clé…"
                               class="w-full rounded-xl border-0 bg-white py-3 pl-12 pr-4 text-sm text-slate-900 shadow-lg shadow-eneam-950/20 placeholder:text-slate-400 focus:ring-2 focus:ring-white">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-eneam-900 shadow-lg shadow-eneam-950/20 transition hover:bg-eneam-50">
                        Rechercher
                    </button>
                </form>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @guest
                        <a href="{{ route('activation.create') }}"
                           class="inline-flex items-center rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                            Première connexion étudiante
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-xs font-semibold text-eneam-100 transition hover:bg-white/10 hover:text-white">
                            Déjà un compte ? Se connecter
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                            Mon espace
                        </a>
                    @endguest
                </div>
            </div>

            <div class="relative hidden items-center lg:flex">
                <div class="w-full rounded-3xl border border-white/15 bg-white/10 p-7 shadow-2xl shadow-eneam-950/20 backdrop-blur-sm xl:p-9">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-eneam-100 ring-1 ring-white/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 11.5h8M8 15h4" />
                        </svg>
                    </div>
                    <p class="mt-6 text-[10px] font-semibold uppercase tracking-[0.2em] text-eneam-200">Bibliothèque numérique ENEAM</p>
                    <h2 class="mt-2 text-2xl font-semibold leading-snug text-white">
                        Un accès simple, pensé pour chaque lecteur.
                    </h2>
                    <p class="mt-3 text-sm leading-relaxed text-eneam-100/90">
                        Repérez les travaux universitaires et consultez-les dans le respect des règles de diffusion.
                    </p>

                    <div class="mt-7 space-y-3">
                        <div class="flex items-start gap-3 rounded-xl bg-white/10 p-3.5 ring-1 ring-white/10">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-300/15 text-emerald-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12.75 4.5 4.5L19 7.5" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-white">Notices accessibles à tous</span>
                                <span class="mt-0.5 block text-xs text-eneam-100/80">Recherche, résumé et informations académiques.</span>
                            </span>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl bg-white/10 p-3.5 ring-1 ring-white/10">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-300/15 text-sky-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.75a3.75 3.75 0 1 1 7.5 0v2.75m-9 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5A1.5 1.5 0 0 1 17.25 21H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-white">Textes intégraux protégés</span>
                                <span class="mt-0.5 block text-xs text-eneam-100/80">Accès selon votre profil et les règles de confidentialité.</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Chiffres clés --}}
    <section class="mx-auto -mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-3 rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-200/70 sm:grid-cols-2 lg:grid-cols-4 lg:p-5">
            <div class="rounded-xl bg-slate-50 px-4 py-4 ring-1 ring-slate-100">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Mémoires consultables</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-eneam-900">{{ $nbMemoires }}</p>
                <p class="mt-1 text-xs text-slate-500">Notices validées</p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-4 ring-1 ring-slate-100">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Filières représentées</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-eneam-900">{{ $nbFilieres }}</p>
                <p class="mt-1 text-xs text-slate-500">Filières ENEAM</p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-4 ring-1 ring-slate-100">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Promotions</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-eneam-900">{{ $nbAnnees }}</p>
                <p class="mt-1 text-xs text-slate-500">Années académiques</p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-4 ring-1 ring-slate-100">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Auteurs référencés</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-eneam-900">{{ $nbAuteurs }}</p>
                <p class="mt-1 text-xs text-slate-500">Auteurs de mémoires</p>
            </div>
        </div>
    </section>

    {{-- Mémoires récents --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eneam-eyebrow">Fonds documentaire</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Derniers mémoires déposés</h2>
                <p class="mt-1 text-sm text-slate-500">Les notices les plus récentes du catalogue.</p>
            </div>
            <a href="{{ route('catalogue.index') }}" class="eneam-link text-sm">Voir tout le catalogue →</a>
        </div>

        @if ($recents->isEmpty())
            <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <p class="text-sm font-medium text-slate-700">Le catalogue est en cours de constitution.</p>
                <p class="mt-1 text-xs text-slate-500">Les premiers mémoires apparaîtront ici après validation.</p>
            </div>
        @else
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($recents as $memoire)
                    @include('catalogue.partials.carte', ['memoire' => $memoire])
                @endforeach
            </div>
        @endif
    </section>

    {{-- Règles d'accès --}}
    <section class="border-y border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="eneam-eyebrow">Règles d'accès</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Un accès différencié selon le profil</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">
                    Les pages de garde sont publiques. Le texte intégral est réservé aux étudiants
                    ENEAM et au personnel autorisé, dans le respect de la confidentialité des travaux.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                    <span class="inline-flex rounded-full bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-600 ring-1 ring-slate-200">Visiteur</span>
                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Catalogue et page de garde</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Recherche, notices, résumés et pages de garde. Aucun accès au PDF complet.
                    </p>
                </div>
                <div class="rounded-2xl border border-eneam-200 bg-eneam-50/60 p-5">
                    <span class="inline-flex rounded-full bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-eneam-700 ring-1 ring-eneam-200">Étudiant ENEAM</span>
                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Texte intégral encadré</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        Accès aux mémoires validés selon la confidentialité et l'embargo, avec historique de consultation.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                    <span class="inline-flex rounded-full bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-600 ring-1 ring-slate-200">Personnel</span>
                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Accès complet journalisé</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Bibliothèque et administration : consultation de tous les mémoires et suivi des accès.
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
