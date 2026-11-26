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

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="#regles-acces"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-eneam-900 shadow-lg shadow-eneam-950/20 transition hover:bg-eneam-50">
                        Comprendre les accès
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0 6-6m-6 6-6-6" />
                        </svg>
                    </a>

                    @guest
                        <a href="{{ route('activation.create') }}"
                           class="inline-flex items-center rounded-xl border border-white/30 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                            Activer mon compte
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center rounded-xl border border-white/30 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                            Mon espace
                        </a>
                    @endguest
                </div>

                @guest
                    <p class="mt-4 text-xs text-eneam-100/80">
                        Déjà activé ?
                        <a href="{{ route('login') }}" class="font-semibold text-white underline-offset-4 hover:underline">
                            Se connecter
                        </a>
                    </p>
                @endguest
            </div>

            <div class="relative hidden items-center lg:flex">
                <div class="w-full rounded-3xl border border-white/15 bg-white/10 p-7 shadow-2xl shadow-eneam-950/20 backdrop-blur-sm xl:p-9">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-eneam-100 ring-1 ring-white/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 11.5h8M8 15h4" />
                        </svg>
                    </div>
                    <p class="mt-6 text-[10px] font-semibold uppercase tracking-[0.2em] text-eneam-200">Le fonds en chiffres</p>
                    <h2 class="mt-2 text-2xl font-semibold leading-snug text-white">
                        Un patrimoine qui s'enrichit à chaque promotion.
                    </h2>
                    <p class="mt-3 text-sm leading-relaxed text-eneam-100/90">
                        Notices validées, filières de formation et auteurs référencés :
                        l'état du fonds documentaire en un coup d'œil.
                    </p>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-3xl font-semibold tracking-tight text-white">{{ $nbMemoires }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-[0.08em] text-eneam-100/80">Mémoires consultables</p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-3xl font-semibold tracking-tight text-white">{{ $nbFilieres }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-[0.08em] text-eneam-100/80">Filières de l'école</p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-3xl font-semibold tracking-tight text-white">{{ $nbAnnees }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-[0.08em] text-eneam-100/80">Promotions couvertes</p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-3xl font-semibold tracking-tight text-white">{{ $nbAuteurs }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-[0.08em] text-eneam-100/80">Auteurs référencés</p>
                        </div>
                    </div>

                    <p class="mt-4 text-[11px] leading-relaxed text-eneam-100/70">
                        Mémoires et auteurs : notices visibles avec votre profil.
                        @if ($nbFilieresCouvertes >= $nbFilieres)
                            Les {{ $nbFilieres }} filières de l'école sont représentées dans le fonds.
                        @else
                            {{ $nbFilieres }} filières au référentiel, dont {{ $nbFilieresCouvertes }} déjà représentée{{ $nbFilieresCouvertes > 1 ? 's' : '' }} dans les notices visibles.
                        @endif
                    </p>
                </div>
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
            <div class="mt-8 grid gap-3 lg:grid-cols-2">
                @foreach ($recents as $memoire)
                    @include('catalogue.partials.carte-compacte', ['memoire' => $memoire])
                @endforeach
            </div>
        @endif
    </section>

    {{-- Règles d'accès --}}
    <section id="regles-acces" class="scroll-mt-20 border-y border-slate-200 bg-white">
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
