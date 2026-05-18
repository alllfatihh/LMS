@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 focus:border-brand-red focus:ring-brand-red rounded-xl shadow-sm transition-all']) }}>
