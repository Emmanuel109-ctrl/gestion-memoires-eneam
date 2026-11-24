<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-base font-semibold text-slate-900">File de validation</h1>
            <p class="text-xs text-slate-500">Contrôlez la conformité des mémoires soumis.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="À vérifier" :value="$aVerifier" hint="En attente de contrôle" icon="clock" tone="amber" />
            <x-stat-card label="Validés ce mois" :value="$validesCeMois" hint="Décisions enregistrées" icon="check" tone="emerald" />
            <x-stat-card label="Rejetés ce mois" :value="$rejetesCeMois" hint="Avec motif communiqué" icon="book" tone="rose" />
        </section>

        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Mémoires à contrôler</h2>
                <p class="text-xs text-slate-500">Les dépôts les plus anciens apparaissent en premier.</p>
            </div>

            @if ($memoires->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-slate-700">Aucun mémoire en attente de vérification</p>
                    <p class="mt-1 text-xs text-slate-500">Les nouveaux dépôts apparaîtront ici.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Titre</th>
                                <th class="px-5 py-3 font-semibold">Auteur</th>
                                <th class="px-5 py-3 font-semibold">Filière</th>
                                <th class="px-5 py-3 font-semibold">Déposé le</th>
                                <th class="px-5 py-3 font-semibold">Statut</th>
                                <th class="px-5 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($memoires as $memoire)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="max-w-xs px-5 py-3">
                                        <p class="truncate font-medium text-slate-800">{{ $memoire->titre }}</p>
                                        <p class="text-xs text-slate-500">{{ $memoire->annee?->libelle ?? 'Année non renseignée' }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $memoire->etudiant?->name ?? $memoire->auteur_nom ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $memoire->filiere?->code ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                        {{ $memoire->date_depot?->translatedFormat('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3"><x-status-badge :statut="$memoire->statut" /></td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('validations.show', $memoire) }}"
                                           class="inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                            Examiner
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $memoires->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
