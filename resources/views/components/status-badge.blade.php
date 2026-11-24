@props(['statut'])

@php
    $statuts = [
        'soumis'          => ['Soumis', 'bg-slate-100 text-slate-700 ring-slate-200'],
        'en_verification' => ['En vérification', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'a_corriger'      => ['À corriger', 'bg-orange-50 text-orange-700 ring-orange-200'],
        'valide'          => ['Validé', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'rejete'          => ['Rejeté', 'bg-rose-50 text-rose-700 ring-rose-200'],
    ];

    [$libelle, $classes] = $statuts[$statut] ?? [$statut, 'bg-slate-100 text-slate-700 ring-slate-200'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset '.$classes]) }}>
    {{ $libelle }}
</span>
