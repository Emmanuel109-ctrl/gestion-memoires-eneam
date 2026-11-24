@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'book',
    'tone' => 'indigo',
])

@php
    $tons = [
        'indigo'  => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
        'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
        'amber'   => 'bg-amber-50 text-amber-600 ring-amber-100',
        'rose'    => 'bg-rose-50 text-rose-600 ring-rose-100',
        'slate'   => 'bg-slate-100 text-slate-600 ring-slate-200',
    ];
    $classeIcone = $tons[$tone] ?? $tons['indigo'];
@endphp

<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold leading-none text-slate-900">{{ $value }}</p>
            @if ($hint)
                <p class="mt-2 text-xs text-slate-500">{{ $hint }}</p>
            @endif
        </div>

        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 {{ $classeIcone }}">
            <x-nav-icon :name="$icon" />
        </span>
    </div>
</div>
