<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Connexion</h1>
        <p class="mt-1 text-sm text-slate-500">
            Étudiants : utilisez votre matricule. Personnel : utilisez votre adresse e-mail.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="identifiant" value="Matricule ou adresse e-mail" />
            <x-text-input id="identifiant"
                          class="mt-1 block w-full"
                          type="text"
                          name="identifiant"
                          :value="old('identifiant')"
                          required
                          autofocus
                          autocomplete="username"
                          placeholder="ETU-2024-001 ou nom@eneam.bj" />
            <x-input-error :messages="$errors->get('identifiant')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password"
                          class="mt-1 block w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-sm text-slate-600">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-slate-600 underline hover:text-slate-900" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">
            Se connecter
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Créer un compte étudiant
        </a>
    </p>
</x-guest-layout>
