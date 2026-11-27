<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Administration</p>
            <h1 class="text-base font-semibold text-slate-900">Historique des décisions</h1>
            <p class="text-xs text-slate-500">Journal des décisions et des retours de validation documentaire.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex justify-end">
            <a href="{{ route('validations.historique.export', request()->only(['q', 'statut'])) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-eneam-200 bg-white px-4 py-2.5 text-sm font-semibold text-eneam-800 shadow-sm hover:bg-eneam-50">
                Exporter en CSV
            </a>
        </div>

        <form method="GET" action="{{ route('validations.historique') }}"
              class="grid gap-3 rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-200/70 sm:grid-cols-[1fr_220px_auto]">
            <div>
                <label for="q" class="sr-only">Rechercher un mémoire ou un étudiant</label>
                <input id="q" name="q" type="search" value="{{ $filtres['q'] ?? '' }}"
                       placeholder="Titre du mémoire ou nom de l’étudiant"
                       class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-eneam-600 focus:ring-eneam-600">
            </div>
            <div>
                <label for="statut" class="sr-only">Filtrer par décision</label>
                <select id="statut" name="statut"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-eneam-600 focus:ring-eneam-600">
                    <option value="">Toutes les décisions</option>
                    <option value="valide" @selected(($filtres['statut'] ?? '') === 'valide')>Validé</option>
                    <option value="a_corriger" @selected(($filtres['statut'] ?? '') === 'a_corriger')>À corriger</option>
                    <option value="rejete" @selected(($filtres['statut'] ?? '') === 'rejete')>Rejeté</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-eneam-900">
                    Filtrer
                </button>
                @if (! empty($filtres['q']) || ! empty($filtres['statut']))
                    <a href="{{ route('validations.historique') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Effacer
                    </a>
                @endif
            </div>
        </form>

        <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Décisions enregistrées</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $validations->total() }} décision{{ $validations->total() === 1 ? '' : 's' }}</p>
            </div>

            @if ($validations->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-slate-700">Aucune décision trouvée</p>
                    <p class="mt-1 text-xs text-slate-500">Les décisions prises sur les dossiers apparaîtront ici.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Date</th>
                                <th class="px-5 py-3 font-semibold">Mémoire / étudiant</th>
                                <th class="px-5 py-3 font-semibold">Décision</th>
                                <th class="px-5 py-3 font-semibold">Responsable</th>
                                <th class="px-5 py-3 font-semibold">Motif ou commentaire</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($validations as $validation)
                                <tr class="align-top hover:bg-slate-50/70">
                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-600">
                                        {{ $validation->date_decision?->translatedFormat('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="max-w-xs px-5 py-4">
                                        @if ($validation->memoire)
                                            <a href="{{ route('catalogue.show', $validation->memoire) }}"
                                               class="font-medium text-eneam-800 hover:underline">
                                                {{ $validation->memoire->titre }}
                                            </a>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $validation->memoire->etudiant?->name ?? $validation->memoire->auteur_nom ?? 'Auteur non renseigné' }}
                                            </p>
                                        @else
                                            <span class="text-slate-500">Mémoire supprimé</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4"><x-status-badge :statut="$validation->statut" /></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-600">
                                        {{ $validation->validateur?->name ?? 'Compte supprimé' }}
                                    </td>
                                    <td class="max-w-sm px-5 py-4 text-xs leading-relaxed text-slate-600">
                                        {{ $validation->motif ?: 'Aucun commentaire.' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $validations->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
