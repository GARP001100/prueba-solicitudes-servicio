@props([
    'variant' => 'neutral',
])

@php
    $classes = match ($variant) {
        'success' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'danger' => 'bg-red-100 text-red-700 ring-red-600/20',
        'warning' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
        'info' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
        'primary' => 'bg-indigo-100 text-indigo-700 ring-indigo-600/20',
        default => 'bg-slate-100 text-slate-700 ring-slate-600/20',
    };
@endphp

<span
    {{ $attributes->class([
        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset',
        $classes,
    ]) }}
>
    {{ $slot }}
</span>
