@props(['size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-8 w-8 rounded-lg text-xs',
        'md' => 'h-10 w-10 rounded-xl text-sm',
        'lg' => 'h-12 w-12 rounded-2xl text-base',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    {{ $attributes->class([
        'relative flex shrink-0 items-center justify-center overflow-hidden bg-blue-950 font-black text-white shadow-md',
        $sizeClass,
    ]) }}
    aria-label="{{ config('app.name') }}"
>
    <span>JP</span>
    <span class="absolute bottom-0 h-1 w-full bg-amber-400" aria-hidden="true"></span>
</div>
