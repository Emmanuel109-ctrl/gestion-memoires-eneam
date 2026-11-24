<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Mot de passe oublié</h1>
        <p class="mt-1 text-sm text-slate-500">
            Indiquez votre adresse e-mail : nous vous enverrons un lien pour choisir un nouveau mot de passe.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email"
                          :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Envoyer le lien de réinitialisation
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Retour à la connexion
        </a>
    </p>
</x-guest-layout>
