<x-guest-layout>
    <div class="mb-7">
        <p class="eneam-eyebrow">Inscription étudiante</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Créer mon compte</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Un seul compte par matricule. Votre matricule à 8 chiffres devient votre identifiant de connexion à la bibliothèque.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nom et prénoms" />
            <x-text-input id="name" class="mt-1.5 block w-full" type="text" name="name"
                          :value="old('name')" required autofocus autocomplete="name"
                          placeholder="Ex. Awa HOUNKPE" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="matricule" value="Matricule ENEAM" />
            <x-text-input id="matricule" class="mt-1.5 block w-full" type="text" name="matricule"
                          :value="old('matricule')" required autocomplete="username"
                          placeholder="12345678" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" />
            <p class="mt-1.5 text-xs text-slate-500">Exactement 8 chiffres, sans lettre ni tiret.</p>
            <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email"
                          :value="old('email')" required autocomplete="email"
                          placeholder="prenom.nom@eneam.bj" />
            <p class="mt-1.5 text-xs text-slate-500">Sert uniquement à récupérer votre compte et à vous notifier.</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="password" value="Mot de passe" />
                <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password"
                              required autocomplete="new-password" placeholder="8 caractères minimum" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmation" />
                <x-text-input id="password_confirmation" class="mt-1.5 block w-full" type="password"
                              name="password_confirmation" required autocomplete="new-password"
                              placeholder="Répétez le mot de passe" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <x-primary-button class="w-full">
            Créer mon compte
        </x-primary-button>
    </form>

    <div class="mt-7 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">
        Vous avez déjà un compte ?
        <a href="{{ route('login') }}" class="eneam-link">Se connecter</a>
    </div>
</x-guest-layout>
