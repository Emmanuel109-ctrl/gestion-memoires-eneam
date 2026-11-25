@php
    $badge = match ($memoire->confidentialite) {
        'public' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'interne' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'restreint' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-slate-50 text-slate-600 ring-slate-200',
    };
@endphp

<a href="{{ route('catalogue.show', $memoire) }}"
   class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-slate-200/70 transition hover:-translate-y-0.5 hover:shadow-lg">
    <div class="relative aspect-[3/4] overflow-hidden bg-slate-100">
        <img src="{{ route('catalogue.couverture', $memoire) }}"
             alt="Page de garde : {{ $memoire->titre }}"
             loading="lazy"
             class="h-full w-full object-cover object-top transition duration-300 group-hover:scale-[1.02]">

        <span class="absolute left-3 top-3 inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold ring-1 ring-inset {{ $badge }}">
            {{ $memoire->libelleConfidentialite() }}
        </span>

        @if ($memoire->date_embargo !== null && $memoire->estEnEmbargo())
            <span class="absolute right-3 top-3 inline-flex rounded-full bg-slate-900/80 px-2.5 py-1 text-[10px] font-semibold text-white">
                Embargo
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4">
        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-eneam-700">
            {{ $memoire->filiere?->nom ?? 'Filière non renseignée' }}
        </p>
        <h3 class="mt-2 line-clamp-3 text-sm font-semibold leading-snug text-slate-900 group-hover:text-eneam-800">
            {{ $memoire->titre }}
        </h3>
        <p class="mt-2 line-clamp-1 text-xs text-slate-500">{{ $memoire->auteur_nom ?? 'Auteur non renseigné' }}</p>

        <div class="mt-auto flex items-center justify-between gap-2 pt-4 text-[11px] text-slate-500">
            <span>{{ $memoire->annee?->libelle ?? $memoire->date_soutenance?->format('Y') }}</span>
            <span class="inline-flex items-center gap-1 font-semibold text-eneam-700">
                Consulter
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </span>
        </div>
    </div>
</a>
