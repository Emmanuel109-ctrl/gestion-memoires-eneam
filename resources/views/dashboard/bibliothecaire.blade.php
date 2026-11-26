<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Bibliothèque</p>
            <h1 class="text-base font-semibold text-slate-900">Tableau de bord — Bibliothèque</h1>
            <p class="text-xs text-slate-500">Pilotage des dépôts et de la vérification documentaire</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Indicateurs --}}
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-stat-card label="Mémoires enregistrés" :value="$nbTotal" hint="Fonds documentaire" icon="book" tone="eneam" />
            <x-stat-card label="En attente" :value="$nbEnAttente" hint="Dossiers à traiter" icon="clock" tone="amber" />
            <x-stat-card label="À corriger" :value="$nbACorriger" hint="Retour étudiant" icon="book" tone="rose" />
            <x-stat-card label="Validés" :value="$nbValides" hint="Consultables au catalogue" icon="check" tone="emerald" />
            <x-stat-card label="Rejetés" :value="$nbRejetes" hint="Non retenus" icon="book" tone="slate" />
        </section>

        {{-- Priorités de traitement --}}
        <section class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl bg-white px-4 py-3 shadow-soft ring-1 ring-slate-200/70">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Communauté</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $nbEtudiants }} étudiant(s) actif(s)
                </p>
                <p class="mt-0.5 text-[11px] text-slate-500">Autorisés à déposer cette année.</p>
            </div>

            <div class="rounded-xl px-4 py-3 shadow-soft ring-1 {{ $joursFile !== null && $joursFile >= 7 ? 'bg-amber-50 ring-amber-200' : 'bg-white ring-slate-200/70' }}">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] {{ $joursFile !== null && $joursFile >= 7 ? 'text-amber-700' : 'text-slate-400' }}">Ancienneté de la file</p>
                <p class="mt-1 text-sm font-semibold {{ $joursFile !== null && $joursFile >= 7 ? 'text-amber-900' : 'text-slate-800' }}">
                    @if ($joursFile === null)
                        File vide
                    @elseif ($joursFile === 0)
                        Dossier du jour
                    @else
                        {{ $joursFile }} jour(s) d'attente
                    @endif
                </p>
                <p class="mt-0.5 text-[11px] {{ $joursFile !== null && $joursFile >= 7 ? 'text-amber-700' : 'text-slate-500' }}">
                    Dépôt en attente le plus ancien.
                </p>
            </div>

            <div class="rounded-xl bg-white px-4 py-3 shadow-soft ring-1 ring-slate-200/70">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Activité</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $decisionsSemaine }} décision(s)
                </p>
                <p class="mt-0.5 text-[11px] text-slate-500">Validations et rejets sur 7 jours.</p>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Répartition --}}
            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Répartition des dossiers</h2>
                        <p class="mt-1 text-xs text-slate-500">État du fonds documentaire</p>
                    </div>
                    @if (Route::has('memoires.index'))
                        <a href="{{ route('memoires.index') }}"
                           class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-200">
                            Voir tous les mémoires →
                        </a>
                    @endif
                </div>

                @php
                    $etats = [
                        'soumis'          => ['Soumis', 'bg-slate-400'],
                        'en_verification' => ['En vérification', 'bg-amber-400'],
                        'a_corriger'      => ['À corriger', 'bg-orange-400'],
                        'valide'          => ['Validés', 'bg-emerald-500'],
                        'rejete'          => ['Rejetés', 'bg-rose-500'],
                    ];
                @endphp

                <div class="mt-5 grid grid-cols-2 gap-3">
                    @foreach ($etats as $cle => [$libelle, $couleur])
                        @php
                            $nombre = (int) ($repartition[$cle] ?? 0);
                            $pourcentage = $nbTotal > 0 ? round($nombre * 100 / $nbTotal) : 0;
                        @endphp
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full {{ $couleur }}"></span>
                                <span class="text-xs font-medium text-slate-600">{{ $libelle }}</span>
                            </div>
                            <p class="mt-2 text-2xl font-semibold leading-none text-slate-900">{{ $nombre }}</p>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full {{ $couleur }}" style="width: {{ $pourcentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Activités récentes --}}
            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Activités récentes</h2>
                        <p class="mt-1 text-xs text-slate-500">Dépôts et décisions de validation</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-600">
                        Aujourd'hui
                    </span>
                </div>

                @php
                    $pastilles = [
                        'emerald' => 'bg-emerald-500',
                        'rose'    => 'bg-rose-500',
                        'orange'  => 'bg-orange-400',
                        'amber'   => 'bg-amber-400',
                        'eneam'  => 'bg-eneam-600',
                        'indigo' => 'bg-eneam-600',
                    ];
                @endphp

                @if ($activites->isEmpty())
                    <div class="py-10 text-center">
                        <p class="text-sm font-medium text-slate-700">Aucune activité pour le moment</p>
                        <p class="mt-1 text-xs text-slate-500">Les dépôts et validations apparaîtront ici.</p>
                    </div>
                @else
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($activites as $activite)
                            <li class="flex gap-3 py-3">
                                <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $pastilles[$activite['ton']] ?? 'bg-slate-400' }}"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-800">{{ $activite['titre'] }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $activite['sous_titre'] }}</p>
                                </div>
                                <span class="whitespace-nowrap text-[11px] text-slate-400">
                                    {{ $activite['date']?->diffForHumans() ?? '—' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        {{-- File de vérification --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">File de vérification documentaire</h2>
                    <p class="text-xs text-slate-500">Dépôts les plus anciens en premier</p>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                    {{ $nbEnAttente }} en attente
                </span>
            </div>

            @if ($file->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-slate-700">Aucun mémoire à vérifier</p>
                    <p class="mt-1 text-xs text-slate-500">Vous êtes à jour. Les nouveaux dépôts apparaîtront ici.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Titre</th>
                                <th class="px-5 py-3 font-semibold">Étudiant</th>
                                <th class="px-5 py-3 font-semibold">Filière</th>
                                <th class="px-5 py-3 font-semibold">Année</th>
                                <th class="px-5 py-3 font-semibold">Déposé le</th>
                                <th class="px-5 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($file as $memoire)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-3">
                                        <p class="max-w-sm truncate font-medium text-slate-800">{{ $memoire->titre }}</p>
                                        <p class="text-xs text-slate-500">{{ $memoire->type_diplome ?? 'Mémoire' }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="text-slate-700">{{ $memoire->etudiant?->name ?? $memoire->auteur_nom ?? '—' }}</p>
                                        <p class="text-xs text-slate-500">{{ $memoire->etudiant?->matricule ?? '' }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $memoire->filiere?->nom ?? '—' }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $memoire->annee?->libelle ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                        {{ $memoire->date_depot?->translatedFormat('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @if (Route::has('validations.show'))
                                            <a href="{{ route('validations.show', $memoire) }}"
                                               class="font-semibold text-eneam-600 hover:text-eneam-800">
                                                Examiner
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </div>
</x-app-layout>
