<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-base font-semibold text-slate-900">Mon espace étudiant</h1>
            <p class="text-xs text-slate-500">Suivi de mes dépôts de mémoires</p>
        </div>
    </x-slot>

    @php($etudiant = auth()->user())

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-6 text-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Bienvenue</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $etudiant->name }}</h2>
                    <p class="mt-1 text-sm text-indigo-100">
                        Matricule : <span class="font-semibold">{{ $etudiant->matricule ?? 'non renseigné' }}</span>
                    </p>
                </div>

                @if (Route::has('memoires.create'))
                    <a href="{{ route('memoires.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                        <x-nav-icon name="upload" class="h-4 w-4" />
                        Déposer un mémoire
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-xs font-medium text-indigo-50 ring-1 ring-white/20">
                        <x-nav-icon name="upload" class="h-4 w-4" />
                        Dépôt disponible au lot 2
                    </span>
                @endif
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-stat-card label="Mes dépôts" :value="$nbDepots" hint="Tous statuts" icon="book" tone="indigo" />
            <x-stat-card label="Validés" :value="$nbValides" hint="Consultables" icon="check" tone="emerald" />
            <x-stat-card label="En attente" :value="$nbEnAttente" hint="Soumis ou en vérification" icon="clock" tone="amber" />
            <x-stat-card label="À corriger" :value="$nbACorriger" hint="Correction demandée" icon="book" tone="rose" />
            <x-stat-card label="Rejetés" :value="$nbRejetes" hint="Non retenus" icon="book" tone="slate" />
        </section>

        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Mes derniers dépôts</h2>
                <p class="text-xs text-slate-500">Les 5 mémoires les plus récents</p>
            </div>

            @if ($derniers->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-slate-700">Vous n'avez encore déposé aucun mémoire</p>
                    <p class="mt-1 text-xs text-slate-500">
                        Le formulaire de dépôt sera disponible très bientôt.
                    </p>
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
                            @foreach ($derniers as $memoire)
                                <tr class="hover:bg-slate-50/70">
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

        <p class="rounded-xl border border-dashed border-slate-300 bg-white/60 px-4 py-3 text-xs text-slate-500">
            Votre matricule est votre identifiant de connexion. Il est déclaré lors de l'inscription et pourra être vérifié par la bibliothèque.
        </p>
    </div>
</x-app-layout>
