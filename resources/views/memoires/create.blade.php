<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Espace étudiant</p>
            <h1 class="text-base font-semibold text-slate-900">Déposer un mémoire</h1>
            <p class="text-xs text-slate-500">Votre document sera vérifié avant toute publication.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('memoires.store') }}" enctype="multipart/form-data"
              class="space-y-6 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70 sm:p-7">
            @csrf

            <div class="rounded-xl border border-eneam-100 bg-eneam-50/60 p-4 text-sm leading-relaxed text-eneam-900">
                Le PDF reste privé pendant l’examen. Il ne sera proposé au catalogue qu’après validation par la bibliothèque.
            </div>

            <div>
                <x-input-label for="titre" value="Titre du mémoire" />
                <x-text-input id="titre" name="titre" type="text" class="mt-1.5 block w-full"
                              :value="old('titre')" required maxlength="255" />
                <x-input-error :messages="$errors->get('titre')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <x-input-label for="type_diplome" value="Diplôme préparé" />
                    <select id="type_diplome" name="type_diplome" required class="eneam-input mt-1.5">
                        <option value="Licence" @selected(old('type_diplome', 'Licence') === 'Licence')>Licence (Bac+3)</option>
                        <option value="DECOGEF" @selected(old('type_diplome') === 'DECOGEF')>DECOGEF</option>
                    </select>
                    <x-input-error :messages="$errors->get('type_diplome')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="filiere_id" value="Filière" />
                    <select id="filiere_id" name="filiere_id" required class="eneam-input mt-1.5">
                        <option value="">Choisir une filière</option>
                        @foreach ($filieres as $filiere)
                            <option value="{{ $filiere->id }}" @selected((string) old('filiere_id') === (string) $filiere->id)>
                                {{ $filiere->nom }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('filiere_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="annee_id" value="Année académique" />
                    <select id="annee_id" name="annee_id" required class="eneam-input mt-1.5" @disabled($annees->isEmpty())>
                        <option value="">Choisir l’année en cours</option>
                        @foreach ($annees as $annee)
                            <option value="{{ $annee->id }}" @selected((string) old('annee_id') === (string) $annee->id)>
                                {{ $annee->libelle }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('annee_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="resume" value="Résumé (facultatif)" />
                <textarea id="resume" name="resume" rows="4" maxlength="10000"
                          class="eneam-input mt-1.5">{{ old('resume') }}</textarea>
                <x-input-error :messages="$errors->get('resume')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="mots_cles" value="Mots-clés (facultatif)" />
                <x-text-input id="mots_cles" name="mots_cles" type="text" class="mt-1.5 block w-full"
                              :value="old('mots_cles')" maxlength="1000"
                              placeholder="Ex. gestion, numérique, Bénin" />
                <x-input-error :messages="$errors->get('mots_cles')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="fichier" value="Fichier PDF" />
                <input id="fichier" name="fichier" type="file" accept="application/pdf,.pdf" required
                       class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                <p class="mt-1.5 text-xs text-slate-500">PDF uniquement, 50 Mo maximum.</p>
                <x-input-error :messages="$errors->get('fichier')" class="mt-2" />
            </div>

            @if ($annees->isEmpty())
                <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                    Aucun exercice académique n’est ouvert au dépôt. Contactez la bibliothèque.
                </p>
            @endif

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('memoires.miennes') }}"
                   class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Annuler
                </a>
                <button type="submit" @disabled($annees->isEmpty())
                        class="inline-flex items-center rounded-xl bg-eneam-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900 disabled:cursor-not-allowed disabled:opacity-50">
                    Envoyer pour vérification
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
