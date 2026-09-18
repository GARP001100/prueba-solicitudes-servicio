@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
])

<section
    {{ $attributes->class([
        'overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm',
    ]) }}
>
    @if ($title || $subtitle || isset($actions))
        <header class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                @if ($title)
                    <h2 class="font-semibold text-slate-900">
                        {{ $title }}
                    </h2>
                @endif

                @if ($subtitle)
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            @isset($actions)
                <div class="shrink-0">
                    {{ $actions }}
                </div>
            @endisset
        </header>
    @endif

    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="border-t border-slate-200 bg-slate-50 px-5 py-3">
            {{ $footer }}
        </footer>
    @endisset
</section>
