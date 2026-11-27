<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Administration</p>
            <h1 class="text-base font-semibold text-slate-900">Comptes du personnel</h1>
            <p class="text-xs text-slate-500">Créer, activer ou désactiver les comptes de l'administration et de la bibliothèque.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600">
                <span class="inline-flex items-center gap-2 rounded-xl bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ $personnel->where('actif', true)->count() }} compte{{ $personnel->where('actif', true)->count() === 1 ? '' : 's' }} actif{{ $personnel->where('actif', true)->count() === 1 ? '' : 's' }}
                </span>
                <span class="inline-flex items-center gap-2 rounded-xl bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200">
                    {{ $nbEtudiants }} étudiant{{ $nbEtudiants === 1 ? '' : 's' }} activé{{ $nbEtudiants === 1 ? '' : 's' }}
                </span>
            </div>

            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-eneam-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-eneam-900">
                <x-nav-icon name="users" class="h-4 w-4" />
                Nouveau compte
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Personnel habilité</h2>
                <p class="text-xs text-slate-500">
                    Les étudiants activent eux-mêmes leur compte depuis l'annuaire ENEAM ; cette page ne gère que le personnel.
                </p>
            </div>

            @if ($personnel->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-slate-500">Aucun compte du personnel enregistré.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nom</th>
                                <th class="px-5 py-3 font-semibold">Identifiant</th>
                                <th class="px-5 py-3 font-semibold">Rôle</th>
                                <th class="px-5 py-3 font-semibold">État</th>
                                <th class="px-5 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($personnel as $membre)
                                <tr>
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800">{{ $membre->name }}</p>
                                        @if ((int) $membre->id === (int) auth()->id())
                                            <p class="text-[11px] text-slate-400">Votre compte</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $membre->email }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $membre->isAdmin() ? 'bg-eneam-50 text-eneam-800' : 'bg-emerald-50 text-emerald-700' }}">
                                            {{ $membre->libelleRole() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($membre->actif)
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Actif</span>
                                        @else
                                            <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Désactivé</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @if ((int) $membre->id === (int) auth()->id())
                                            <span class="text-xs text-slate-400">—</span>
                                        @else
                                            <form method="POST" action="{{ route('users.toggle', $membre) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="font-semibold {{ $membre->actif ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                    {{ $membre->actif ? 'Désactiver' : 'Réactiver' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-xs leading-relaxed text-slate-500 shadow-soft">
            <p class="font-semibold text-slate-700">Règles de sécurité appliquées</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <li>Un administrateur ne peut pas désactiver son propre compte.</li>
                <li>Le dernier administrateur actif ne peut pas être désactivé.</li>
                <li>Un compte désactivé est immédiatement déconnecté, même si une session est ouverte.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
