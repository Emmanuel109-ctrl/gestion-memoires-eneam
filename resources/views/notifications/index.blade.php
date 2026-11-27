<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Activité</p>
            <h1 class="text-base font-semibold text-slate-900">Notifications</h1>
            <p class="text-xs text-slate-500">Décisions de validation, nouveaux dépôts et rappels de la bibliothèque.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-600">
                {{ $notifications->total() }} notification{{ $notifications->total() === 1 ? '' : 's' }}
                @if ($nonLues > 0)
                    <span class="ml-1 inline-flex rounded-full bg-eneam-50 px-2.5 py-1 text-xs font-semibold text-eneam-800 ring-1 ring-inset ring-eneam-200">
                        {{ $nonLues }} non lue{{ $nonLues === 1 ? '' : 's' }}
                    </span>
                @endif
            </p>

            @if ($nonLues > 0)
                <form method="POST" action="{{ route('notifications.toutLire') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <x-nav-icon name="check" class="h-4 w-4" />
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        @if ($notifications->isEmpty())
            <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-nav-icon name="bell" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-base font-semibold text-slate-800">Aucune notification pour le moment</p>
                <p class="mt-1 text-sm text-slate-500">
                    Vous serez informé ici dès qu'une décision concerne vos dépôts ou qu'un nouveau mémoire est soumis.
                </p>
            </section>
        @else
            <section class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70">
                @foreach ($notifications as $notification)
                    @php
                        $donnees = $notification->data;
                        $nonLue = $notification->read_at === null;
                    @endphp

                    <form method="POST" action="{{ route('notifications.lire', $notification->id) }}"
                          class="block border-b border-slate-100 last:border-b-0">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-start gap-4 px-5 py-4 text-left transition hover:bg-slate-50 {{ $nonLue ? 'bg-eneam-50/40' : '' }}">
                            <span class="mt-1.5 flex h-2.5 w-2.5 shrink-0 rounded-full {{ $nonLue ? 'bg-eneam-600' : 'bg-slate-200' }}"></span>

                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-slate-900">
                                        {{ $donnees['libelle'] ?? 'Notification' }}
                                    </span>
                                    <time class="text-[11px] text-slate-400" datetime="{{ $notification->created_at?->toIso8601String() }}">
                                        {{ $notification->created_at?->format('d/m/Y H:i') }}
                                    </time>
                                </span>

                                <span class="mt-1 block line-clamp-2 text-sm text-slate-600">
                                    {{ $donnees['titre'] ?? 'Consulter la fiche concernée.' }}
                                </span>

                                @if (! empty($donnees['motif']))
                                    <span class="mt-2 block rounded-lg bg-amber-50 px-3 py-2 text-xs leading-relaxed text-amber-800 ring-1 ring-inset ring-amber-200">
                                        Motif : {{ $donnees['motif'] }}
                                    </span>
                                @endif
                            </span>

                            <span class="mt-1 shrink-0 text-slate-300">
                                <x-nav-icon name="chevron-right" class="h-4 w-4" />
                            </span>
                        </button>
                    </form>
                @endforeach
            </section>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
