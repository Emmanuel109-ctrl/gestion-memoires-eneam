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
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-eneam-200">Bienvenue</p>
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
                        Dépôt disponible au lot 2
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

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70 xl:col-span-2">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Mes derniers dépôts</h2>
                    <p class="text-xs text-slate-500">Les 5 mémoires les plus récents</p>
                </div>

                @if ($derniers->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">Vous n'avez encore déposé aucun mémoire</p>
                        <p class="mt-1 text-xs text-slate-500">Le formulaire de dépôt sera disponible très bientôt.</p>
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

            <aside class="space-y-4">
                <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                    <h2 class="text-sm font-semibold text-slate-900">Mon identifiant</h2>
                    <p class="mt-1 text-xs text-slate-500">Il vous suit durant tout votre parcours.</p>

                    <div class="mt-4 rounded-xl bg-eneam-50 p-4 text-center ring-1 ring-eneam-100">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Matricule ENEAM</p>
                        <p class="mt-1 text-2xl font-bold tracking-[0.14em] text-eneam-900">{{ $etudiant->matricule ?? '—' }}</p>
                    </div>

                    <p class="mt-3 text-[11px] leading-relaxed text-slate-500">
                        Déclaré à l'inscription, il sera vérifié par la bibliothèque. Un matricule erroné peut bloquer la validation de vos dépôts.
                    </p>
                </section>

                <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
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
    </div>
</x-app-layout>
