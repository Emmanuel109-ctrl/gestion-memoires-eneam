<x-public-layout title="Catalogue des mémoires">
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="eneam-eyebrow">Catalogue</p>
            <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Mémoires de soutenance</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">
                        Consultez les notices et les pages de garde. Le texte intégral est accessible
                        aux étudiants ENEAM authentifiés, selon les règles de confidentialité.
                    </p>
                </div>
                <p class="rounded-full bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                    {{ $memoires->total() }} mémoire{{ $memoires->total() > 1 ? 's' : '' }}
                </p>
            </div>

            <form method="GET" action="{{ route('catalogue.index') }}" class="mt-7 grid gap-3 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70 lg:grid-cols-[1fr_220px_180px_auto]">
                <div class="relative">
                    <label for="q" class="sr-only">Recherche</label>
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                    </svg>
                    <input id="q" name="q" type="search" value="{{ $recherche }}"
                           placeholder="Titre, auteur, mot-clé…"
                           class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 text-sm shadow-sm focus:border-eneam-600 focus:ring-eneam-600">
                </div>

                <div>
                    <label for="filiere" class="sr-only">Filière</label>
                    <select id="filiere" name="filiere" class="w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm shadow-sm focus:border-eneam-600 focus:ring-eneam-600">
                        <option value="">Toutes les filières</option>
                        @foreach ($filieres as $filiere)
                            <option value="{{ $filiere->id }}" @selected((int) ($filtres['filiere'] ?? 0) === $filiere->id)>
                                {{ $filiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="annee" class="sr-only">Année académique</label>
                    <select id="annee" name="annee" class="w-full rounded-xl border-slate-300 bg-white py-2.5 text-sm shadow-sm focus:border-eneam-600 focus:ring-eneam-600">
                        <option value="">Toutes les années</option>
                        @foreach ($annees as $annee)
                            <option value="{{ $annee->id }}" @selected((int) ($filtres['annee'] ?? 0) === $annee->id)>
                                {{ $annee->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                        Filtrer
                    </button>
                    @if ($recherche !== '' || ! empty($filtres['filiere']) || ! empty($filtres['annee']))
                        <a href="{{ route('catalogue.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                           title="Réinitialiser les filtres">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($memoires->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <p class="text-base font-semibold text-slate-800">Aucun mémoire ne correspond à votre recherche</p>
                <p class="mt-2 text-sm text-slate-500">Modifiez les mots-clés ou réinitialisez les filtres.</p>
                <a href="{{ route('catalogue.index') }}" class="eneam-link mt-4 inline-block text-sm">Voir tous les mémoires</a>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($memoires as $memoire)
                    @include('catalogue.partials.carte', ['memoire' => $memoire])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $memoires->links() }}
            </div>
        @endif
    </section>

    @guest
        <section class="mx-auto max-w-7xl px-4 pb-6 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 rounded-2xl bg-eneam-900 px-6 py-6 text-white sm:flex-row sm:items-center">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-200">Étudiants ENEAM</p>
                    <p class="mt-1 text-sm font-semibold">Connectez-vous pour accéder au texte intégral des mémoires.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('login') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-eneam-900 transition hover:bg-eneam-50">
                        Se connecter
                    </a>
                    <a href="{{ route('activation.create') }}" class="rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 transition hover:bg-white/15">
                        Première connexion
                    </a>
                </div>
            </div>
        </section>
    @endguest
</x-public-layout>
