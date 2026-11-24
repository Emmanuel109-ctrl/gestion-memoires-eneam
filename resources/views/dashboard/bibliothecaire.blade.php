<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-base font-semibold text-slate-900">Tableau de bord — Bibliothèque</h1>
            <p class="text-xs text-slate-500">Suivi des dépôts et des validations</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="À vérifier" :value="$nbEnAttente" hint="Priorité du jour" icon="clock" tone="amber" />
            <x-stat-card label="Validés" :value="$nbValides" hint="Consultables au catalogue" icon="check" tone="emerald" />
            <x-stat-card label="Rejetés" :value="$nbRejetes" hint="Motif à communiquer" icon="book" tone="rose" />
            <x-stat-card label="Total" :value="$nbTotal" hint="Fonds documentaire" icon="book" tone="indigo" />
        </section>

        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">File de vérification</h2>
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
                                    <td class="px-5 py-3 text-slate-600">{{ $memoire->filiere?->code ?? '—' }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $memoire->annee?->libelle ?? '—' }}</td>
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
            L'acceptation et le rejet des mémoires (avec motif) seront branchés au lot 2.
        </p>
    </div>
</x-app-layout>
