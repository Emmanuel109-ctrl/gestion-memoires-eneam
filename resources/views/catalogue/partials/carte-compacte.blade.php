@php
    $badge = match ($memoire->confidentialite) {
        'public' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'interne' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'restreint' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-slate-50 text-slate-600 ring-slate-200',
    };
@endphp

<a href="{{ route('catalogue.show', $memoire) }}"
   class="group flex items-center gap-4 rounded-2xl bg-white p-3 shadow-soft ring-1 ring-slate-200/70 transition hover:-translate-y-0.5 hover:shadow-lg sm:p-4">
    {{-- Aperçu compact : la page de garde reste lisible sans dominer la page. --}}
    <div class="h-28 w-20 shrink-0 overflow-hidden rounded-lg bg-slate-100 ring-1 ring-slate-200">
        <img src="{{ route('catalogue.couverture', $memoire) }}"
             alt="Page de garde : {{ $memoire->titre }}"
             loading="lazy"
             width="80" height="112"
             class="h-full w-full object-cover object-top transition duration-300 group-hover:scale-[1.03]">
    </div>

    <div class="min-w-0 flex-1">
        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-eneam-700">
            {{ $memoire->filiere?->nom ?? 'Filière non renseignée' }}
        </p>
        <h3 class="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-slate-900 group-hover:text-eneam-800">
            {{ $memoire->titre }}
        </h3>
        <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $memoire->auteur_nom ?? 'Auteur non renseigné' }}</p>

        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
            <span>{{ $memoire->annee?->libelle ?? $memoire->date_soutenance?->format('Y') }}</span>
            <span class="inline-flex rounded-full px-2 py-0.5 font-semibold ring-1 ring-inset {{ $badge }}">
                {{ $memoire->libelleConfidentialite() }}
            </span>
            @if ($memoire->date_embargo !== null && $memoire->estEnEmbargo())
                <span class="font-semibold text-rose-600">Embargo</span>
            @endif
        </div>
    </div>

    <svg class="hidden h-4 w-4 shrink-0 text-eneam-700 transition group-hover:translate-x-0.5 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
    </svg>
</a>
