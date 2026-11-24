<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-base font-semibold text-slate-900">Tableau de bord — Administration</h1>
            <p class="text-xs text-slate-500">Vue d'ensemble de la bibliothèque des mémoires</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Mémoires" :value="$nbMemoires" hint="Tous statuts confondus" icon="book" tone="indigo" />
            <x-stat-card label="En attente" :value="$nbEnAttente" hint="À vérifier par la bibliothèque" icon="clock" tone="amber" />
            <x-stat-card label="Filières" :value="$nbFilieres" hint="Référentiel actif" icon="folder" tone="emerald" />
            <x-stat-card label="Années académiques" :value="$nbAnnees" hint="Historique disponible" icon="calendar" tone="slate" />
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-1">
                <h2 class="text-sm font-semibold text-slate-900">Répartition par statut</h2>
                <p class="mt-1 text-xs text-slate-500">État du fonds documentaire</p>

                @php
                    $etats = [
                        'soumis'          => ['Soumis', 'bg-slate-400'],
                        'en_verification' => ['En vérification', 'bg-amber-400'],
                        'a_corriger'      => ['À corriger', 'bg-orange-400'],
                        'valide'          => ['Validé', 'bg-emerald-500'],
                        'rejete'          => ['Rejeté', 'bg-rose-500'],
                    ];
                @endphp

                <div class="mt-4 space-y-4">
                    @foreach ($etats as $cle => [$libelle, $couleur])
                        @php
                            $nombre = (int) ($repartition[$cle] ?? 0);
                            $pourcentage = $nbMemoires > 0 ? round($nombre * 100 / $nbMemoires) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700">{{ $libelle }}</span>
                                <span class="text-slate-500">{{ $nombre }} · {{ $pourcentage }} %</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $couleur }}" style="width: {{ $pourcentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    Le catalogue et les comptes utilisateurs arrivent au lot 2 et 3.
                </p>
            </section>

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Derniers dépôts</h2>
                        <p class="text-xs text-slate-500">Les 6 mémoires les plus récents</p>
                    </div>
                </div>

                @if ($dernieres->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">Aucun mémoire pour le moment</p>
                        <p class="mt-1 text-xs text-slate-500">Les dépôts des étudiants apparaîtront ici.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Titre</th>
                                    <th class="px-5 py-3 font-semibold">Filière</th>
                                    <th class="px-5 py-3 font-semibold">Année</th>
                                    <th class="px-5 py-3 font-semibold">Statut</th>
                                    <th class="px-5 py-3 font-semibold">Déposé le</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($dernieres as $memoire)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-5 py-3">
                                            <p class="max-w-xs truncate font-medium text-slate-800">{{ $memoire->titre }}</p>
                                            <p class="text-xs text-slate-500">
                                                {{ $memoire->etudiant?->name ?? $memoire->auteur_nom ?? 'Auteur non renseigné' }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-3 text-slate-600">{{ $memoire->filiere?->code ?? '—' }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $memoire->annee?->libelle ?? '—' }}</td>
                                        <td class="px-5 py-3"><x-status-badge :statut="$memoire->statut" /></td>
                                        <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                            {{ $memoire->date_depot?->translatedFormat('d/m/Y') ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
