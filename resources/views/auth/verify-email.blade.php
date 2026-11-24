<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Vérification de l'adresse e-mail</h1>
        <p class="mt-1 text-sm text-slate-500">
            Merci pour votre inscription. Cliquez sur le lien que nous venons de vous envoyer par e-mail
            pour vérifier votre adresse.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-200">
            Un nouveau lien de vérification a été envoyé à l'adresse fournie lors de l'inscription.
        </div>
    @endif

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Renvoyer l'e-mail de vérification</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-600 underline hover:text-slate-900">
                Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
