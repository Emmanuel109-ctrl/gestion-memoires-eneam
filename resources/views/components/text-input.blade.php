@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-slate-300 bg-white text-sm shadow-sm transition placeholder:text-slate-400 focus:border-eneam-600 focus:ring-eneam-600']) }}>
