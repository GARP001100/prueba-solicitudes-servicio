<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                class="rounded-xl border border-slate-200 p-2 text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800 lg:hidden"
                x-on:click="sidebarOpen = true"
                aria-label="Abrir menú"
            >
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-lg font-bold text-slate-900">
                    {{ $header ?? 'Dashboard' }}
                </h1>

                <p class="hidden text-xs capitalize text-slate-500 sm:block">
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="relative rounded-xl p-2.5 text-slate-500 transition hover:bg-blue-50 hover:text-blue-800"
                aria-label="Notificaciones"
            >
                <i data-lucide="bell" class="h-5 w-5"></i>

                <span
                    class="absolute right-2 top-2 h-2 w-2 rounded-full bg-amber-400 ring-2 ring-white"
                    aria-hidden="true"
                ></span>
            </button>

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-blue-50"
            >
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-800">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>

                <span class="hidden max-w-32 truncate text-sm font-semibold text-slate-700 sm:block">
                    {{ auth()->user()->name ?? 'Usuario' }}
                </span>

                <i
                    data-lucide="chevron-down"
                    class="hidden h-4 w-4 text-slate-400 sm:block"
                ></i>
            </a>
        </div>
    </div>
</header>
