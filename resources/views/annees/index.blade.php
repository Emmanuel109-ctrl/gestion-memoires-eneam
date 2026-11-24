<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-base font-semibold text-slate-900">Années académiques</h1>
            <p class="text-xs text-slate-500">Gérez les périodes et choisissez l’année active.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-1">
            <h2 class="text-sm font-semibold text-slate-900">Ajouter une année</h2>
            <form method="POST" action="{{ route('annees.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <x-input-label for="libelle" value="Libellé" />
                    <x-text-input id="libelle" name="libelle" class="mt-1 block w-full"
                                  :value="old('libelle')" placeholder="2026-2027" required />
                    <x-input-error :messages="$errors->get('libelle')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="date_debut" value="Date de début" />
                    <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full"
                                  :value="old('date_debut')" required />
                    <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="date_fin" value="Date de fin" />
                    <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full"
                                  :value="old('date_fin')" required />
                    <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                </div>

                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="active" value="1"
                           @checked(old('active'))
                           class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Définir comme année active</span>
                </label>

                <x-primary-button class="w-full justify-center">Créer l’année</x-primary-button>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 xl:col-span-2">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Années enregistrées</h2>
                <p class="text-xs text-slate-500">Une seule année peut être active à la fois. La première année créée sera activée automatiquement.</p>
            </div>

            @if ($annees->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-slate-500">Aucune année académique enregistrée.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Libellé</th>
                                <th class="px-5 py-3 font-semibold">Période</th>
                                <th class="px-5 py-3 font-semibold">État</th>
                                <th class="px-5 py-3 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($annees as $annee)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-slate-800">{{ $annee->libelle }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                        {{ $annee->date_debut->format('d/m/Y') }} – {{ $annee->date_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($annee->active)
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @unless ($annee->active)
                                            <form method="POST" action="{{ route('annees.activate', $annee) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="font-medium text-indigo-600 hover:text-indigo-800">
                                                    Activer
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endunless
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
