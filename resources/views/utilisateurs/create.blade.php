<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Administration</p>
            <h1 class="text-base font-semibold text-slate-900">Nouveau compte du personnel</h1>
            <p class="text-xs text-slate-500">Le compte est actif dès sa création ; l'intéressé pourra se connecter avec son adresse e-mail.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('users.store') }}"
              class="space-y-5 rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200/70">
            @csrf

            <div class="rounded-xl border border-eneam-100 bg-eneam-50/60 p-4 text-sm leading-relaxed text-eneam-900">
                <p class="font-semibold">Comptes étudiants exclus</p>
                <p class="mt-1 text-xs text-eneam-800/80">
                    Les étudiants activent leur compte avec leur matricule et leur e-mail d'annuaire depuis
                    « Première connexion ». Cette page est réservée aux agents de l'administration et de la bibliothèque.
                </p>
            </div>

            <div>
                <x-input-label for="name" value="Nom complet" />
                <x-text-input id="name" name="name" class="mt-1.5 block w-full"
                              :value="old('name')" required autofocus placeholder="Ex. Awa HOUNKPE" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Adresse e-mail professionnelle" />
                <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full"
                              :value="old('email')" required placeholder="prenom.nom@eneam.bj" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="role" value="Rôle" />
                <select id="role" name="role" required
                        class="mt-1.5 block w-full rounded-xl border-slate-300 bg-white text-sm shadow-sm focus:border-eneam-600 focus:ring-eneam-600">
                    <option value="bibliothecaire" @selected(old('role', 'bibliothecaire') === 'bibliothecaire')>Bibliothécaire</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrateur</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" value="Mot de passe" />
                    <x-text-input id="password" name="password" type="password" class="mt-1.5 block w-full"
                                  required autocomplete="new-password" />
                    <p class="mt-1.5 text-xs text-slate-500">8 caractères minimum.</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                  class="mt-1.5 block w-full" required autocomplete="new-password" />
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <x-primary-button>Créer le compte</x-primary-button>
                <a href="{{ route('users.index') }}"
                   class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
