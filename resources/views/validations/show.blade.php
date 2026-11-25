<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Vérification documentaire</p>
            <h1 class="text-base font-semibold text-slate-900">Contrôle de conformité</h1>
            <p class="text-xs text-slate-500">Examinez le dossier et enregistrez votre décision.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <a href="{{ route('validations.index') }}" class="text-sm font-medium text-eneam-600 hover:text-eneam-800">
            ← Retour à la file de validation
        </a>

        <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Mémoire à examiner</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $memoire->titre }}</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $memoire->etudiant?->name ?? $memoire->auteur_nom ?? 'Auteur non renseigné' }}
                        @if ($memoire->etudiant?->matricule)
                            · {{ $memoire->etudiant->matricule }}
                        @endif
                    </p>
                </div>
                <x-status-badge :statut="$memoire->statut" />
            </div>

            <dl class="mt-5 grid gap-4 border-t border-slate-100 pt-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-xs text-slate-500">Filière</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->filiere?->nom ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Année académique</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->annee?->libelle ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Type de diplôme</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->type_diplome ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Déposé le</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $memoire->date_depot?->translatedFormat('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>

            <div class="mt-4 rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-medium text-slate-500">Résumé</p>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $memoire->resume ?: 'Aucun résumé renseigné.' }}</p>
                <p class="mt-3 text-xs font-medium text-slate-500">Fichier PDF associé</p>
                <p class="mt-1 break-all text-sm text-slate-700">{{ $memoire->fichier_pdf }}</p>
                <p class="mt-2 text-xs text-slate-500">L’aperçu en ligne du PDF sera disponible avec le module de lecture.</p>
            </div>
        </section>

        <form method="POST" action="{{ route('validations.update', $memoire) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <h2 class="text-sm font-semibold text-slate-900">Points de contrôle documentaires</h2>
                <p class="mt-1 text-xs text-slate-500">Cochez les vérifications conformes au dossier.</p>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                        <input type="checkbox" name="conforme_version_papier" value="1"
                               @checked(old('conforme_version_papier', $validation?->conforme_version_papier))
                               class="mt-0.5 rounded border-slate-300 text-eneam-600 focus:ring-eneam-500">
                        <span>
                            <span class="block text-sm font-medium text-slate-800">Version papier conforme</span>
                            <span class="mt-1 block text-xs text-slate-500">Le document numérique correspond à l’exemplaire papier.</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                        <input type="checkbox" name="format_conforme" value="1"
                               @checked(old('format_conforme', $validation?->format_conforme))
                               class="mt-0.5 rounded border-slate-300 text-eneam-600 focus:ring-eneam-500">
                        <span>
                            <span class="block text-sm font-medium text-slate-800">Format conforme</span>
                            <span class="mt-1 block text-xs text-slate-500">Le PDF est lisible et respecte les exigences de format.</span>
                        </span>
                    </label>
                </div>
            </section>

            <section class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
                <h2 class="text-sm font-semibold text-slate-900">Décision</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    @foreach ([
                        'valide' => ['Valider', 'border-emerald-200 bg-emerald-50 text-emerald-800'],
                        'a_corriger' => ['Demander une correction', 'border-amber-200 bg-amber-50 text-amber-800'],
                        'rejete' => ['Rejeter', 'border-rose-200 bg-rose-50 text-rose-800'],
                    ] as $valeur => [$libelle, $style])
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border p-3 text-sm font-medium {{ $style }}">
                            <input type="radio" name="statut" value="{{ $valeur }}"
                                   @checked(old('statut', $validation?->statut) === $valeur)
                                   class="border-slate-300 text-eneam-600 focus:ring-eneam-500">
                            {{ $libelle }}
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('statut')" class="mt-2" />

                <div class="mt-4">
                    <x-input-label for="motif" value="Commentaire / motif (obligatoire en cas de correction ou de rejet)" />
                    <textarea id="motif" name="motif" rows="4"
                              class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-eneam-500 focus:ring-eneam-500"
                              maxlength="5000">{{ old('motif', $validation?->motif) }}</textarea>
                    <x-input-error :messages="$errors->get('motif')" class="mt-2" />
                </div>

                <div class="mt-5 flex flex-wrap justify-end gap-3">
                    <a href="{{ route('validations.index') }}"
                       class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Annuler
                    </a>
                    <x-primary-button>Enregistrer la décision</x-primary-button>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>
