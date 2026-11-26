<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Bibliothèque</p>
            <h1 class="text-base font-semibold text-slate-900">Gestion des mémoires</h1>
            <p class="text-xs text-slate-500">Rechercher les dépôts et consulter leur dossier.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <form method="GET" action="{{ route('memoires.index') }}"
              class="grid gap-3 rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-200/70 sm:grid-cols-[1fr_220px_auto]">
            <div>
                <label for="q" class="sr-only">Rechercher un mémoire</label>
                <input id="q" name="q" value="{{ $filtres['q'] ?? '' }}"
                       placeholder="Titre ou auteur"
                       class="eneam-input">
            </div>
            <div>
                <label for="statut" class="sr-only">Statut</label>
                <select id="statut" name="statut" class="eneam-input">
                    <option value="">Tous les statuts</option>
                    @foreach (['soumis' => 'Soumis', 'en_verification' => 'En vérification', 'a_corriger' => 'À corriger', 'valide' => 'Validé', 'rejete' => 'Rejeté'] as $statut => $libelle)
                        <option value="{{ $statut }}" @selected(($filtres['statut'] ?? '') === $statut)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-eneam-900">
                    Rechercher
                </button>
                @if (($filtres['q'] ?? '') !== '' || ($filtres['statut'] ?? '') !== '')
                    <a href="{{ route('memoires.index') }}"
                       class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Effacer
                    </a>
                @endif
            </div>
        </form>

        <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Tous les dossiers</h2>
                    <p class="text-xs text-slate-500">{{ $memoires->total() }} mémoire{{ $memoires->total() === 1 ? '' : 's' }}</p>
                </div>
                @if (Route::has('validations.index'))
                    <a href="{{ route('validations.index') }}" class="text-xs font-semibold text-eneam-700 hover:text-eneam-900">
                        File de vérification →
                    </a>
                @endif
            </div>

            @if ($memoires->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-semibold text-slate-700">Aucun mémoire trouvé</p>
                    <p class="mt-1 text-xs text-slate-500">Essayez une autre recherche ou un autre statut.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/70 text-left text-[10px] uppercase tracking-[0.12em] text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Mémoire / auteur</th>
                                <th class="px-5 py-3 font-semibold">Filière</th>
                                <th class="px-5 py-3 font-semibold">Année</th>
                                <th class="px-5 py-3 font-semibold">Statut</th>
                                <th class="px-5 py-3 font-semibold">Déposé le</th>
                                <th class="px-5 py-3 text-right font-semibold">Dossier</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($memoires as $memoire)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="max-w-sm px-5 py-3">
                                        <p class="truncate font-medium text-slate-800">{{ $memoire->titre }}</p>
                                        <p class="mt-0.5 truncate text-xs text-slate-500">
                                            {{ $memoire->etudiant?->name ?? $memoire->auteur_nom ?? 'Auteur non renseigné' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-slate-600">{{ $memoire->filiere?->nom ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-xs text-slate-600">{{ $memoire->annee?->libelle ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3"><x-status-badge :statut="$memoire->statut" /></td>
                                    <td class="whitespace-nowrap px-5 py-3 text-xs text-slate-600">{{ $memoire->date_depot?->translatedFormat('d/m/Y') ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right">
                                        <a href="{{ route('catalogue.show', $memoire) }}" class="text-xs font-semibold text-eneam-700 hover:text-eneam-900">
                                            Consulter →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-5 py-4">{{ $memoires->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
