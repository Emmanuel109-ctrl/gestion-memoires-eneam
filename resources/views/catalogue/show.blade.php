<x-public-layout :title="$memoire->titre">
    @php
        $badge = match ($memoire->confidentialite) {
            'public' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'interne' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'restreint' => 'bg-rose-50 text-rose-700 ring-rose-200',
            default => 'bg-slate-50 text-slate-600 ring-slate-200',
        };
    @endphp

    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500" aria-label="Fil d'Ariane">
                <a href="{{ route('home') }}" class="hover:text-eneam-800">Accueil</a>
                <span>/</span>
                <a href="{{ route('catalogue.index') }}" class="hover:text-eneam-800">Catalogue</a>
                <span>/</span>
                <span class="line-clamp-1 text-slate-700">{{ $memoire->titre }}</span>
            </nav>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[180px_1fr] xl:grid-cols-[200px_1fr]">
            {{-- Page de garde --}}
            <div class="mx-auto w-full max-w-[200px] lg:max-w-none">
                <div class="overflow-hidden rounded-2xl bg-white p-2 shadow-soft ring-1 ring-slate-200/70">
                    <div class="overflow-hidden rounded-xl bg-slate-100">
                        <img src="{{ route('catalogue.couverture', $memoire) }}"
                             alt="Page de garde : {{ $memoire->titre }}"
                             class="aspect-[3/4] w-full object-cover object-top">
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-white p-4 text-xs leading-relaxed text-slate-500 shadow-soft ring-1 ring-slate-200/70">
                    <p class="font-semibold text-slate-700">À propos de l'accès</p>
                    <p class="mt-2">
                        Seule la page de garde est publique. Le fichier PDF complet reste stocké
                        dans un espace privé et n'est jamais exposé directement.
                    </p>
                </div>
            </div>

            {{-- Notice --}}
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-semibold ring-1 ring-inset {{ $badge }}">
                        {{ $memoire->libelleConfidentialite() }}
                    </span>
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-600">
                        {{ $memoire->type_diplome ?? 'Mémoire' }}
                    </span>
                    @if ($memoire->date_embargo !== null && $memoire->estEnEmbargo())
                        <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-[10px] font-semibold text-white">
                            Embargo jusqu'au {{ $memoire->date_embargo->format('d/m/Y') }}
                        </span>
                    @endif
                    @if (! $memoire->estValide())
                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-[10px] font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                            {{ $memoire->libelleStatut() }}
                        </span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-semibold leading-tight tracking-tight text-slate-900 sm:text-4xl">
                    {{ $memoire->titre }}
                </h1>

                <p class="mt-4 text-sm text-slate-600">
                    <span class="font-semibold text-slate-800">{{ $memoire->auteur_nom ?? 'Auteur non renseigné' }}</span>
                    @if ($memoire->filiere)
                        <span class="text-slate-400"> · </span>{{ $memoire->filiere->nom }}
                    @endif
                </p>

                <dl class="mt-7 grid gap-4 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Année académique</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->annee?->libelle ?? 'Non renseignée' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Date de soutenance</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->date_soutenance?->format('d/m/Y') ?? 'Non renseignée' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Cote</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->cote ?? 'Non attribuée' }}</dd>
                    </div>
                </dl>

                @if ($memoire->resume)
                    <div class="mt-7">
                        <h2 class="text-sm font-semibold text-slate-900">Résumé</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $memoire->resume }}</p>
                    </div>
                @endif

                @if ($memoire->mots_cles)
                    <div class="mt-6">
                        <h2 class="text-sm font-semibold text-slate-900">Mots-clés</h2>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach (array_filter(array_map('trim', explode(',', $memoire->mots_cles))) as $mot)
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $mot }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Panneau d'accès --}}
                <div class="mt-8 rounded-2xl border border-eneam-200 bg-eneam-50/70 p-5">
                    @if ($peutLire)
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-eneam-700">Accès autorisé</p>
                        <h2 class="mt-1 text-base font-semibold text-slate-900">Consulter le mémoire</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600">
                            Votre profil autorise la consultation du texte intégral. Toute consultation est journalisée.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('catalogue.lire', $memoire) }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 rounded-xl bg-eneam-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h5.25A2.25 2.25 0 0 1 12 6.75v12A2.25 2.25 0 0 1 9.75 21H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75Zm19.5 0A2.25 2.25 0 0 0 19.5 4.5h-5.25A2.25 2.25 0 0 0 12 6.75v12A2.25 2.25 0 0 0 14.25 21h5.25a2.25 2.25 0 0 0 2.25-2.25V6.75Z" />
                                </svg>
                                Lire en ligne
                            </a>

                            @if ($peutTelecharger)
                                <a href="{{ route('catalogue.telecharger', $memoire) }}"
                                   class="inline-flex items-center gap-2 rounded-xl border border-eneam-300 bg-white px-5 py-2.5 text-sm font-semibold text-eneam-800 transition hover:bg-eneam-50">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Télécharger le PDF
                                </a>
                            @endif
                        </div>
                    @elseif (auth()->guest())
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-eneam-700">Texte intégral réservé</p>
                        <h2 class="mt-1 text-base font-semibold text-slate-900">Connectez-vous avec votre compte ENEAM</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600">
                            Les visiteurs consultent la notice et la page de garde. L'accès au PDF est réservé
                            aux étudiants et au personnel autorisés.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="rounded-xl bg-eneam-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-eneam-900">
                                Se connecter
                            </a>
                            <a href="{{ route('activation.create') }}" class="rounded-xl border border-eneam-300 bg-white px-5 py-2.5 text-sm font-semibold text-eneam-800 transition hover:bg-eneam-50">
                                Première connexion
                            </a>
                        </div>
                    @else
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-amber-700">Accès non autorisé</p>
                        <h2 class="mt-1 text-base font-semibold text-slate-900">Ce mémoire n'est pas accessible avec votre profil</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600">
                            @if ($memoire->date_embargo !== null && $memoire->estEnEmbargo())
                                Le texte intégral sera disponible le {{ $memoire->date_embargo->format('d/m/Y') }}.
                            @elseif ($memoire->confidentialite === 'restreint')
                                Ce mémoire est classé « accès restreint ». Seuls l'auteur, la bibliothèque
                                et l'administration peuvent le consulter.
                            @else
                                Le mémoire n'est pas encore validé ou son accès est temporairement fermé.
                            @endif
                        </p>
                        <a href="{{ route('catalogue.index') }}" class="eneam-link mt-4 inline-block text-sm">Retour au catalogue</a>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <a href="{{ route('catalogue.index') }}" class="font-semibold text-eneam-700 hover:text-eneam-900">← Retour au catalogue</a>
                    @auth
                        @if (auth()->user()->can('validate', $memoire) && Route::has('validations.show'))
                            <a href="{{ route('validations.show', $memoire) }}" class="font-semibold text-slate-600 hover:text-slate-900">
                                Ouvrir la fiche de contrôle
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
