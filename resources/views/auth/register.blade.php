<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Créer un compte étudiant</h1>
        <p class="mt-1 text-sm text-slate-500">
            Votre matricule à 8 chiffres vous identifie dans la bibliothèque. Indiquez une adresse e-mail valide pour récupérer votre compte.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nom et prénoms" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name"
                          :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="matricule" value="Matricule" />
            <x-text-input id="matricule" class="mt-1 block w-full" type="text" name="matricule"
                          :value="old('matricule')" required autocomplete="username"
                          placeholder="12345678" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" />
            <p class="mt-1 text-xs text-slate-500">Saisissez les 8 chiffres de votre matricule.</p>
            <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email"
                          :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Créer mon compte
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Se connecter
        </a>
    </p>
</x-guest-layout>
