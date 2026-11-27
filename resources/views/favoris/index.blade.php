<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-eneam-700">Ma sélection</p>
            <h1 class="text-base font-semibold text-slate-900">Mes favoris</h1>
            <p class="text-xs text-slate-500">Retrouvez les mémoires que vous avez mis de côté pour vos recherches.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-600">
                {{ $memoires->total() }} mémoire{{ $memoires->total() === 1 ? '' : 's' }} enregistré{{ $memoires->total() === 1 ? '' : 's' }}
            </p>
            <a href="{{ route('catalogue.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <x-nav-icon name="book" class="h-4 w-4" />
                Parcourir le catalogue
            </a>
        </div>

        @if ($memoires->isEmpty())
            <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-nav-icon name="star" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-base font-semibold text-slate-800">Aucun favori pour l'instant</p>
                <p class="mt-1 text-sm text-slate-500">
                    Sur la fiche d'un mémoire, utilisez « Ajouter aux favoris » pour le retrouver ici.
                </p>
                <a href="{{ route('catalogue.index') }}" class="eneam-link mt-4 inline-block text-sm">Explorer le catalogue</a>
            </section>
        @else
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($memoires as $memoire)
                    @include('catalogue.partials.carte', ['memoire' => $memoire])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $memoires->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
