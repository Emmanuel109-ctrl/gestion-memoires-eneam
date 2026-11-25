<x-guest-layout>
    <div class="mb-7">
        <p class="eneam-eyebrow">Espace personnel</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Connexion</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Étudiants, connectez-vous avec votre matricule ENEAM à 8 chiffres. Le personnel utilise son adresse e-mail professionnelle.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="identifiant" value="Matricule ou adresse e-mail" />
            <x-text-input id="identifiant"
                          class="mt-1.5 block w-full"
                          type="text"
                          name="identifiant"
                          :value="old('identifiant')"
                          required
                          autofocus
                          autocomplete="username"
                          placeholder="Ex. 20240001 ou prenom.nom@eneam.bj" />
            <x-input-error :messages="$errors->get('identifiant')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Mot de passe" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-eneam-700 hover:text-eneam-900" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            <x-text-input id="password"
                          class="mt-1.5 block w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="current-password"
                          placeholder="Votre mot de passe" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2.5 text-sm text-slate-600">
            <input id="remember_me" type="checkbox"
                   class="rounded border-slate-300 text-eneam-700 shadow-sm focus:ring-eneam-600"
                   name="remember">
            Rester connecté sur cet appareil
        </label>

        <x-primary-button class="w-full">
            Accéder à mon espace
        </x-primary-button>
    </form>

    <div class="mt-7 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">
        Vous n'avez pas encore de compte ?
        <a href="{{ route('register') }}" class="eneam-link">Créer un compte étudiant</a>
    </div>

    <p class="mt-5 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-relaxed text-slate-500 ring-1 ring-slate-100">
        Le matricule est attribué par l'ENEAM. Il sera vérifié par la bibliothèque lors de la première connexion.
    </p>
</x-guest-layout>
