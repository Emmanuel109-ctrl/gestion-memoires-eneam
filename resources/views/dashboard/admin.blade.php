<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Administration</p>
            <h1 class="text-base font-semibold text-slate-900">Tableau de bord — Administration</h1>
            <p class="text-xs text-slate-500">Vue d'ensemble du fonds documentaire et de la communauté ENEAM</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Année académique en cours :
                <span class="font-semibold text-slate-800">{{ $anneeCourante ?? 'Aucune année active' }}</span>
            </p>
            <div class="flex flex-wrap gap-2">
                @if (Route::has('memoires.index'))
                    <a href="{{ route('memoires.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-eneam-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                        <x-nav-icon name="book" class="h-4 w-4" /> Consulter les mémoires
                    </a>
                @endif
                @if (Route::has('annees.index'))
                    <a href="{{ route('annees.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Référentiels
                    </a>
                @endif
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Mémoires" :value="$nbMemoires" hint="Tous statuts confondus" icon="book" tone="eneam" />
            <x-stat-card label="En attente" :value="$nbEnAttente" hint="Soumis ou en vérification" icon="clock" tone="amber" />
            <x-stat-card label="Validés" :value="$nbValides" hint="Publiés au catalogue" icon="check" tone="emerald" />
            <x-stat-card label="Utilisateurs actifs" :value="$nbUtilisateurs" :hint="$nbEtudiants.' '.($nbEtudiants > 1 ? 'étudiants' : 'étudiant')" icon="users" tone="slate" />
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Répartition par statut</h2>
                        <p class="mt-1 text-xs text-slate-500">État du fonds documentaire</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">{{ $nbMemoires }} dossiers</span>
                </div>

                @php
                    $etats = [
                        'soumis'          => ['Soumis', 'bg-slate-400'],
                        'en_verification' => ['En vérification', 'bg-amber-400'],
                        'a_corriger'      => ['À corriger', 'bg-orange-400'],
                        'valide'          => ['Validé', 'bg-emerald-500'],
                        'rejete'          => ['Rejeté', 'bg-rose-500'],
                    ];
                @endphp

                <div class="mt-5 space-y-4">
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
            </section>

            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <h2 class="text-sm font-semibold text-slate-900">Filières les plus représentées</h2>
                <p class="mt-1 text-xs text-slate-500">Répartition des dépôts par filière</p>

                @php
                    $max = max(1, (int) ($parFiliere->max('memoires_count') ?? 1));
                @endphp

                <div class="mt-5 space-y-3">
                    @forelse ($parFiliere as $filiere)
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-eneam-50 text-[10px] font-bold text-eneam-800">
                                {{ $filiere->code }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="truncate font-medium text-slate-700">{{ $filiere->nom }}</span>
                                    <span class="text-slate-500">{{ $filiere->memoires_count }}</span>
                                </div>
                                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-eneam-600" style="width: {{ round($filiere->memoires_count * 100 / $max) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-xs text-slate-500">Aucune filière renseignée.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Activité récente</h2>
                        <p class="mt-1 text-xs text-slate-500">Dépôts et décisions</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-100">En direct</span>
                </div>

                @php
                    $pastilles = [
                        'emerald' => 'bg-emerald-500',
                        'rose'    => 'bg-rose-500',
                        'orange'  => 'bg-orange-400',
                        'amber'   => 'bg-amber-400',
                        'eneam'   => 'bg-eneam-600',
                        'indigo'  => 'bg-eneam-600',
                    ];
                @endphp

                @if (($activites ?? collect())->isEmpty())
                    <p class="py-10 text-center text-xs text-slate-500">Aucune activité pour le moment.</p>
                @else
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($activites as $activite)
                            <li class="flex gap-3 py-3">
                                <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $pastilles[$activite['ton'] ?? 'eneam'] ?? 'bg-slate-400' }}"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold text-slate-800">{{ $activite['titre'] }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">{{ $activite['sous_titre'] }}</p>
                                </div>
                                <span class="whitespace-nowrap text-[10px] text-slate-400">{{ $activite['date']?->diffForHumans() ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        {{-- Usage du fonds : ce que le tableau de bord ne montre pas ailleurs. --}}
        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <section class="min-w-0 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <h2 class="text-sm font-semibold text-slate-900">Qualité du fonds</h2>
                <p class="mt-1 text-xs text-slate-500">Part des notices validées et poids des fichiers</p>

                <div class="mt-5 flex items-center gap-5">
                    <div class="relative flex h-24 w-24 shrink-0 items-center justify-center rounded-full"
                         style="background: conic-gradient(#059669 {{ $tauxValidation * 3.6 }}deg, #e2e8f0 0deg);">
                        <div class="flex h-[74px] w-[74px] flex-col items-center justify-center rounded-full bg-white">
                            <span class="text-xl font-semibold text-slate-900">{{ $tauxValidation }} %</span>
                            <span class="text-[9px] font-semibold uppercase tracking-[0.1em] text-slate-400">validé</span>
                        </div>
                    </div>
                    <dl class="min-w-0 flex-1 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-slate-500">Consultations</dt>
                            <dd class="font-semibold text-slate-900">{{ number_format($nbConsultations, 0, ',', ' ') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-slate-500">Téléchargements</dt>
                            <dd class="font-semibold text-slate-900">{{ number_format($nbTelechargements, 0, ',', ' ') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-slate-500">Stockage PDF</dt>
                            <dd class="font-semibold text-slate-900">
                                {{ $tailleStockage > 0 ? number_format($tailleStockage / 1048576, 1, ',', ' ') . ' Mo' : '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <p class="mt-5 rounded-xl bg-slate-50 px-3 py-2.5 text-[11px] leading-relaxed text-slate-500 ring-1 ring-slate-100">
                    Les consultations sont journalisées à chaque ouverture du texte intégral.
                    Ce suivi permet de mesurer l'usage réel du fonds, pas seulement son volume.
                </p>
            </section>

            <section class="min-w-0 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Mémoires les plus consultés</h2>
                        <p class="mt-1 text-xs text-slate-500">Classement par nombre d'accès au texte intégral</p>
                    </div>
                    @if (Route::has('memoires.index'))
                        <a href="{{ route('memoires.index') }}" class="text-xs font-semibold text-eneam-700 hover:text-eneam-900">Voir tout →</a>
                    @endif
                </div>

                @php
                    $maxConsultations = max(1, (int) ($topConsultes->max('total') ?? 1));
                @endphp

                @if ($topConsultes->isEmpty())
                    <div class="py-10 text-center">
                        <p class="text-sm font-medium text-slate-700">Aucune consultation enregistrée</p>
                        <p class="mt-1 text-xs text-slate-500">Le classement se remplira dès les premières lectures authentifiées.</p>
                    </div>
                @else
                    <ol class="mt-4 space-y-3">
                        @foreach ($topConsultes as $index => $ligne)
                            <li class="flex items-center gap-3">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-eneam-50 text-[10px] font-bold text-eneam-800">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-medium text-slate-800">
                                        {{ $ligne->memoire?->titre ?? 'Mémoire supprimé' }}
                                    </p>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-eneam-600"
                                             style="width: {{ round($ligne->total * 100 / $maxConsultations) }}%"></div>
                                    </div>
                                </div>
                                <span class="shrink-0 text-[11px] font-semibold text-slate-500">
                                    {{ $ligne->total }} accès
                                </span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Derniers dépôts</h2>
                    <p class="text-xs text-slate-500">Les 6 mémoires les plus récents</p>
                </div>
                @if (Route::has('memoires.index'))
                    <a href="{{ route('memoires.index') }}" class="text-xs font-semibold text-eneam-700 hover:text-eneam-900">Voir tout →</a>
                @endif
            </div>

            @if ($dernieres->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-slate-700">Aucun mémoire pour le moment</p>
                    <p class="mt-1 text-xs text-slate-500">Les dépôts des étudiants apparaîtront ici.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/70 text-left text-[10px] uppercase tracking-[0.12em] text-slate-500">
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
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-5 py-3">
                                        <p class="max-w-xs truncate font-medium text-slate-800">{{ $memoire->titre }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $memoire->etudiant?->name ?? $memoire->auteur_nom ?? 'Auteur non renseigné' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $memoire->filiere?->nom ?? '—' }}</td>
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

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-200/70">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Filières</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $nbFilieres }}</p>
                <p class="mt-1 text-xs text-slate-500">Référentiel actif</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-200/70">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Années académiques</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $nbAnnees }}</p>
                <p class="mt-1 text-xs text-slate-500">Historique disponible</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-200/70">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">À corriger</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $nbACorriger }}</p>
                <p class="mt-1 text-xs text-slate-500">En attente d'une nouvelle version</p>
            </div>
        </div>
    </div>
</x-app-layout>
