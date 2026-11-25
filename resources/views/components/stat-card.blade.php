@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'book',
    'tone' => 'eneam',
])

@php
    $tons = [
        'eneam'   => 'bg-eneam-50 text-eneam-700 ring-eneam-100',
        'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
        'amber'   => 'bg-amber-50 text-amber-600 ring-amber-100',
        'rose'    => 'bg-rose-50 text-rose-600 ring-rose-100',
        'slate'   => 'bg-slate-100 text-slate-600 ring-slate-200',
    ];
    $classeIcone = $tons[$tone] ?? $tons['eneam'];
@endphp

<div class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-200/70">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold leading-none tracking-tight text-slate-900">{{ $value }}</p>
            @if ($hint)
                <p class="mt-2 text-xs text-slate-500">{{ $hint }}</p>
            @endif
        </div>

        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 {{ $classeIcone }}">
            <x-nav-icon :name="$icon" />
        </span>
    </div>
</div>
