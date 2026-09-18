<div
    x-data
    x-cloak
    x-show="$store.adminUi.loading"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-blue-950/70 px-4 backdrop-blur-md"
    role="status"
    aria-live="polite"
    aria-busy="true"
>
    <div
        x-show="$store.adminUi.loading"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="scale-90 opacity-0 translate-y-3"
        x-transition:enter-end="scale-100 opacity-100 translate-y-0"
        class="w-full max-w-xs overflow-hidden rounded-3xl border border-white/20 bg-white p-7 text-center shadow-2xl shadow-blue-950/40"
    >
        <div class="relative mx-auto h-20 w-20">
            <div class="absolute inset-0 animate-ping rounded-full bg-blue-300/30"></div>

            <div class="absolute inset-1 animate-spin rounded-full border-4 border-blue-100 border-t-blue-800"></div>

            <div class="absolute inset-4 flex items-center justify-center rounded-full bg-blue-950 shadow-inner">
                <span class="text-sm font-black tracking-tight text-white">
                    JP
                </span>
            </div>

            <span class="absolute right-1 top-1 h-4 w-4 animate-pulse rounded-full border-2 border-white bg-amber-400 shadow-lg"></span>
        </div>

        <h2 class="mt-5 text-lg font-bold text-slate-900">
            <span x-text="$store.adminUi.loadingTitle"></span>
        </h2>

        <p
            class="mt-2 text-sm leading-6 text-slate-500"
            x-text="$store.adminUi.loadingMessage"
        ></p>

        <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-blue-100">
            <div class="admin-loader-progress h-full rounded-full bg-gradient-to-r from-blue-900 via-blue-600 to-amber-400"></div>
        </div>

        <p class="mt-3 text-xs font-medium text-blue-800">
            No cierres esta ventana
        </p>
    </div>
</div>
