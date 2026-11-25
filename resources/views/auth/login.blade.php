<x-guest-layout>
    <div class="mb-7">
        <p class="eneam-eyebrow">Espace personnel</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Connexion</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Une seule page pour les étudiants et le personnel. Le matricule à 8 chiffres est
            réservé aux étudiants ; le personnel utilise son adresse e-mail professionnelle.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-5 rounded-xl border border-eneam-200 bg-eneam-50/70 p-4">
        <p class="text-sm font-semibold text-slate-800">C'est votre première connexion ?</p>
        <p class="mt-1 text-xs leading-relaxed text-slate-600">
            Activez d'abord votre compte avec votre matricule et votre adresse e-mail. Vous choisirez votre mot de passe pendant cette étape.
        </p>
        <a href="{{ route('activation.create') }}" class="mt-2 inline-flex text-xs font-semibold text-eneam-800 underline-offset-4 hover:underline">
            Activer mon compte étudiant
        </a>
    </div>

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

    <div class="mt-7 space-y-3 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">
        <p>
            <a href="{{ route('catalogue.index') }}" class="text-xs font-medium text-slate-500 hover:text-eneam-800">
                Consulter le catalogue sans compte
            </a>
        </p>
        <p>
            <a href="{{ route('home') }}" class="text-xs font-medium text-slate-500 hover:text-eneam-800">
                ← Retour à l'accueil
            </a>
        </p>
    </div>

    <p class="mt-5 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-relaxed text-slate-500 ring-1 ring-slate-100">
        Le matricule est vérifié auprès de l'annuaire ENEAM. Les comptes du personnel sont créés
        par l'administration : aucune inscription publique n'est proposée.
    </p>
</x-guest-layout>
