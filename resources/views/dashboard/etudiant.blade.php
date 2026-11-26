<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Espace étudiant</p>
            <h1 class="text-base font-semibold text-slate-900">Mon espace étudiant</h1>
            <p class="text-xs text-slate-500">Suivi de mes dépôts de mémoires</p>
        </div>
    </x-slot>

    @php
        $etudiant = auth()->user();
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-eneam-900 via-eneam-800 to-eneam-600 px-6 py-7 text-white shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-eneam-200">Espace personnel</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight">{{ $etudiant->name }}</h2>
                    <p class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-eneam-100">
                        <span>Matricule : <strong class="font-semibold text-white">{{ $etudiant->matricule ?? 'non renseigné' }}</strong></span>
                        <span class="hidden h-1 w-1 rounded-full bg-eneam-300 sm:block"></span>
                        <span>Bibliothèque numérique ENEAM</span>
                    </p>
                </div>

                @if (Route::has('memoires.create'))
                    <a href="{{ route('memoires.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-eneam-800 shadow-sm transition hover:bg-eneam-50">
                        <x-nav-icon name="upload" class="h-4 w-4" />
                        Déposer un mémoire
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-xs font-medium text-eneam-50 ring-1 ring-white/20">
                        <x-nav-icon name="upload" class="h-4 w-4" />
                        Dépôt en ligne bientôt disponible
                    </span>
                @endif
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-stat-card label="Mes dépôts" :value="$nbDepots" hint="Tous statuts" icon="book" tone="eneam" />
            <x-stat-card label="Validés" :value="$nbValides" hint="Consultables" icon="check" tone="emerald" />
            <x-stat-card label="En attente" :value="$nbEnAttente" hint="Soumis ou en vérification" icon="clock" tone="amber" />
            <x-stat-card label="À corriger" :value="$nbACorriger" hint="Correction demandée" icon="book" tone="rose" />
            <x-stat-card label="Rejetés" :value="$nbRejetes" hint="Non retenus" icon="book" tone="slate" />
        </section>

        <div class="grid gap-6 xl:grid-cols-3 xl:items-start">
            <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70 xl:col-span-2">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Mes derniers dépôts</h2>
                    <p class="text-xs text-slate-500">Les 5 mémoires les plus récents</p>
                </div>

                @if ($derniers->isEmpty())
                    <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-eneam-50 text-eneam-700 ring-1 ring-eneam-100">
                            <x-nav-icon name="upload" class="h-6 w-6" />
                        </span>
                        <p class="mt-4 text-sm font-semibold text-slate-800">Votre premier dépôt commence ici</p>
                        <p class="mt-1 max-w-sm text-xs leading-relaxed text-slate-500">
                            Déposez votre mémoire en PDF pour le transmettre à la bibliothèque, puis suivez ici
                            sa vérification et les éventuelles corrections demandées.
                        </p>
                        <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                            <a href="{{ route('catalogue.index') }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-eneam-800 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-eneam-900">
                                Explorer le catalogue
                            </a>
                            <a href="#etapes-depot"
                               class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                Comprendre le circuit
                            </a>
                        </div>
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
                                @foreach ($derniers as $memoire)
                                    <tr class="transition hover:bg-slate-50/70">
                                        <td class="px-5 py-3">
                                            <p class="max-w-sm truncate font-medium text-slate-800">{{ $memoire->titre }}</p>
                                            <p class="text-xs text-slate-500">{{ $memoire->type_diplome ?? 'Mémoire' }}</p>
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

            <aside class="space-y-4">
                <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                    <h2 class="text-sm font-semibold text-slate-900">Mon identifiant</h2>
                    <p class="mt-1 text-xs text-slate-500">Il vous suit durant tout votre parcours.</p>

                    <div class="mt-4 rounded-xl bg-eneam-50 p-4 text-center ring-1 ring-eneam-100">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Matricule ENEAM</p>
                        <p class="mt-1 text-2xl font-bold tracking-[0.14em] text-eneam-900">{{ $etudiant->matricule ?? '—' }}</p>
                    </div>

                    <p class="mt-3 text-[11px] leading-relaxed text-slate-500">
                        Issu de l'annuaire ENEAM, il sera vérifié par la bibliothèque. Un matricule erroné peut bloquer la validation de vos dépôts.
                    </p>
                </section>

                <section id="etapes-depot" class="scroll-mt-24 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                    <h2 class="text-sm font-semibold text-slate-900">Les étapes d'un dépôt</h2>
                    <ol class="mt-4 space-y-3 text-xs text-slate-600">
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-600">1</span>
                            <span><strong class="font-semibold text-slate-800">Soumission</strong> du mémoire au format PDF.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold text-amber-700">2</span>
                            <span><strong class="font-semibold text-slate-800">Vérification</strong> du document et de sa conformité.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[10px] font-bold text-orange-700">3</span>
                            <span><strong class="font-semibold text-slate-800">Correction</strong> si un motif est signalé.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[10px] font-bold text-emerald-700">4</span>
                            <span><strong class="font-semibold text-slate-800">Validation</strong> puis publication au catalogue.</span>
                        </li>
                    </ol>
                </section>
            </aside>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="xl:col-span-2">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">À découvrir dans le catalogue</h2>
                        <p class="text-xs text-slate-500">Les derniers mémoires validés qui peuvent vous intéresser.</p>
                    </div>
                    <a href="{{ route('catalogue.index') }}" class="eneam-link text-xs">Parcourir tout le catalogue →</a>
                </div>

                @if ($suggestions->isEmpty())
                    <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-center">
                        <p class="text-sm font-medium text-slate-700">Aucune nouvelle suggestion pour le moment</p>
                        <p class="mt-1 text-xs text-slate-500">Explorez le catalogue pour trouver d'autres mémoires.</p>
                    </div>
                @else
                    <div class="mt-4 grid gap-3">
                        @foreach ($suggestions as $suggestion)
                            @include('catalogue.partials.carte-compacte', ['memoire' => $suggestion])
                        @endforeach
                    </div>
                @endif
            </section>

            <aside class="space-y-4">
                <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="text-sm font-semibold text-slate-900">Mes dernières consultations</h2>
                        <p class="text-xs text-slate-500">Vos accès récents au texte intégral</p>
                    </div>

                    @if ($consultations->isEmpty())
                        <div class="px-5 py-8 text-center">
                            <p class="text-xs leading-relaxed text-slate-500">
                                Vous n'avez encore consulté aucun mémoire.
                                Vos lectures apparaîtront ici.
                            </p>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-100">
                            @foreach ($consultations as $consultation)
                                @if ($consultation->memoire)
                                    <li>
                                        <a href="{{ route('catalogue.show', $consultation->memoire) }}"
                                           class="flex items-start gap-3 px-5 py-3 transition hover:bg-slate-50/70">
                                            <span class="mt-0.5 inline-flex shrink-0 rounded-full px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.08em] {{ $consultation->type === 'telechargement' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                {{ $consultation->type === 'telechargement' ? 'PDF' : 'Lu' }}
                                            </span>
                                            <span class="min-w-0">
                                                <span class="line-clamp-2 block text-xs font-medium text-slate-800">{{ $consultation->memoire->titre }}</span>
                                                <span class="mt-0.5 block text-[11px] text-slate-500">
                                                    {{ $consultation->memoire->filiere?->nom ?? '—' }}
                                                    · {{ $consultation->created_at?->diffForHumans() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
