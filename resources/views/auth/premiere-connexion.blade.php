<x-guest-layout>
    <div class="mb-7">
        <p class="eneam-eyebrow">Première connexion</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Activer mon compte étudiant</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Cette page est réservée aux étudiants inscrits à l'ENEAM et présents dans l'annuaire
            de la bibliothèque. Munissez-vous de votre matricule et de votre adresse e-mail.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('activation.store') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="matricule" value="Matricule ENEAM (8 chiffres)" />
            <x-text-input id="matricule"
                          class="mt-1.5 block w-full"
                          type="text"
                          name="matricule"
                          inputmode="numeric"
                          maxlength="8"
                          pattern="[0-9]{8}"
                          :value="old('matricule')"
                          required
                          autofocus
                          autocomplete="username"
                          placeholder="Ex. 20240001" />
            <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Adresse e-mail de l'annuaire" />
            <x-text-input id="email"
                          class="mt-1.5 block w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autocomplete="email"
                          placeholder="prenom.nom@eneam.bj" />
            <p class="mt-1.5 text-xs text-slate-500">
                Utilisez l'adresse transmise à la scolarité. Elle doit correspondre au matricule.
            </p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Créer votre mot de passe de connexion" />
            <p class="mt-1.5 text-xs text-slate-500">
                Choisissez-le vous-même : vous l'utiliserez ensuite avec votre matricule pour vous connecter.
            </p>
            <x-text-input id="password"
                          class="mt-1.5 block w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="new-password"
                          placeholder="8 caractères minimum" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation"
                          class="mt-1.5 block w-full"
                          type="password"
                          name="password_confirmation"
                          required
                          autocomplete="new-password"
                          placeholder="Répétez le mot de passe" />
        </div>

        <label for="attestation" class="flex items-start gap-3 rounded-xl bg-slate-50 p-3.5 text-xs leading-relaxed text-slate-600 ring-1 ring-slate-100">
            <input id="attestation" type="checkbox" name="attestation" value="1" @checked(old('attestation'))
                   class="mt-0.5 rounded border-slate-300 text-eneam-700 shadow-sm focus:ring-eneam-600">
            <span>
                J'atteste être l'étudiant titulaire de ce matricule et j'accepte que mon compte
                soit vérifié auprès de l'annuaire ENEAM.
            </span>
        </label>
        <x-input-error :messages="$errors->get('attestation')" />

        <x-primary-button class="w-full">
            Activer mon compte
        </x-primary-button>
    </form>

    <div class="mt-7 space-y-3 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">
        <p>
            Vous avez déjà activé votre compte ?
            <a href="{{ route('login') }}" class="eneam-link">Se connecter</a>
        </p>
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
        Aucune inscription libre n'est possible. Si vos informations ne sont pas reconnues,
        contactez la bibliothèque de l'ENEAM pour mettre à jour l'annuaire.
    </p>
</x-guest-layout>
