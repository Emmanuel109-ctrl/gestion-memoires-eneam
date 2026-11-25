<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Compte</p>
            <h1 class="text-base font-semibold text-slate-900">Mon profil</h1>
            <p class="text-xs text-slate-500">Informations du compte et sécurité</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
