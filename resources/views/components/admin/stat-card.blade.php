@props([
    'title',
    'value',
    'icon' => 'activity',
    'variant' => 'primary',
    'description' => null,
])

@php
    $colors = match ($variant) {
        'success' => 'bg-emerald-100 text-emerald-700',
        'danger' => 'bg-red-100 text-red-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'info' => 'bg-blue-100 text-blue-700',
        default => 'bg-indigo-100 text-indigo-700',
    };
@endphp

<div
    {{ $attributes->class([
        'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm',
    ]) }}
>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">
                {{ $title }}
            </p>

            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                {{ $value }}
            </p>

            @if ($description)
                <p class="mt-2 text-xs text-slate-500">
                    {{ $description }}
                </p>
            @endif
        </div>

        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $colors }}">
            <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
        </div>
    </div>
</div>
