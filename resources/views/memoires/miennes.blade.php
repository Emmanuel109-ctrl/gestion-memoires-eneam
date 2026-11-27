<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Espace étudiant</p>
            <h1 class="text-base font-semibold text-slate-900">Mes mémoires</h1>
            <p class="text-xs text-slate-500">Suivez l’avancement de vos dépôts et les retours de la bibliothèque.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-600">{{ $memoires->total() }} dépôt{{ $memoires->total() === 1 ? '' : 's' }}</p>
            <a href="{{ route('memoires.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                <x-nav-icon name="upload" class="h-4 w-4" />
                Déposer un mémoire
            </a>
        </div>

        @if ($memoires->isEmpty())
            <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                <p class="text-base font-semibold text-slate-800">Aucun dépôt pour le moment</p>
                <p class="mt-2 text-sm text-slate-500">Déposez votre mémoire en PDF pour le transmettre à la bibliothèque.</p>
                <a href="{{ route('memoires.create') }}"
                   class="mt-5 inline-flex rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-eneam-900">
                    Commencer un dépôt
                </a>
            </section>
        @else
            <div class="space-y-4">
                @foreach ($memoires as $memoire)
                    @php($derniereValidation = $memoire->validations->sortByDesc('id')->first())
                    <article class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-sm font-semibold text-slate-900">{{ $memoire->titre }}</h2>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $memoire->filiere?->nom ?? 'Filière non renseignée' }}
                                    · {{ $memoire->annee?->libelle ?? 'Année non renseignée' }}
                                    · Déposé le {{ $memoire->date_depot?->translatedFormat('d/m/Y') ?? '—' }}
                                </p>
                            </div>
                            <x-status-badge :statut="$memoire->statut" />
                        </div>

                        @if ($memoire->statut === 'a_corriger' && $derniereValidation?->motif)
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-amber-800">Corrections demandées</p>
                                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-amber-900">{{ $derniereValidation->motif }}</p>
                            </div>

                            <form method="POST" action="{{ route('memoires.resoumettre', $memoire) }}"
                                  enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-3">
                                @csrf
                                <div class="min-w-0 flex-1">
                                    <label for="fichier-{{ $memoire->id }}" class="block text-xs font-semibold text-slate-700">
                                        Envoyer le PDF corrigé
                                    </label>
                                    <input id="fichier-{{ $memoire->id }}" name="fichier" type="file"
                                           accept="application/pdf,.pdf" required
                                           class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-slate-700">
                                    @error('fichier')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                        class="rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-eneam-900">
                                    Renvoyer
                                </button>
                            </form>
                        @elseif ($derniereValidation?->motif)
                            <p class="mt-3 rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-600">
                                Retour de la bibliothèque : {{ $derniereValidation->motif }}
                            </p>
                        @endif

                        @if ($memoire->validations->isNotEmpty())
                            <details class="mt-4 rounded-xl border border-slate-200 bg-slate-50">
                                <summary class="cursor-pointer px-4 py-3 text-xs font-semibold text-slate-700">
                                    Historique des décisions ({{ $memoire->validations->count() }})
                                </summary>
                                <ol class="space-y-3 border-t border-slate-200 px-4 py-4">
                                    @foreach ($memoire->validations->sortByDesc('date_decision') as $decision)
                                        <li class="border-l-2 border-slate-300 pl-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <x-status-badge :statut="$decision->statut" />
                                                <span class="text-[11px] text-slate-500">
                                                    {{ $decision->date_decision?->translatedFormat('d/m/Y à H:i') ?? 'Date non renseignée' }}
                                                </span>
                                            </div>
                                            @if ($decision->motif)
                                                <p class="mt-2 whitespace-pre-line text-xs leading-relaxed text-slate-600">{{ $decision->motif }}</p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </details>
                        @endif

                        <div class="mt-4 flex flex-wrap items-center gap-4 border-t border-slate-100 pt-3 text-xs">
                            <a href="{{ route('catalogue.show', $memoire) }}"
                               class="font-semibold text-eneam-700 hover:text-eneam-900">
                                Voir le dossier
                            </a>
                            <span class="text-slate-400">
                                Version {{ $memoire->versions->max('numero_version') ?? 1 }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{ $memoires->links() }}
        @endif
    </div>
</x-app-layout>
