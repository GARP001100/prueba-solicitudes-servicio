@if (session('success'))
    <div
        class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"
        role="alert"
    >
        <i data-lucide="circle-check" class="mt-0.5 h-5 w-5 shrink-0"></i>

        <div>
            <p class="font-medium">
                Operación exitosa
            </p>

            <p class="mt-1 text-sm">
                {{ session('success') }}
            </p>
        </div>
    </div>
@endif

@if (session('error'))
    <div
        class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"
        role="alert"
    >
        <i data-lucide="circle-x" class="mt-0.5 h-5 w-5 shrink-0"></i>

        <div>
            <p class="font-medium">
                No fue posible completar la operación
            </p>

            <p class="mt-1 text-sm">
                {{ session('error') }}
            </p>
        </div>
    </div>
@endif

@if (session('warning'))
    <div
        class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800"
        role="alert"
    >
        <i data-lucide="triangle-alert" class="mt-0.5 h-5 w-5 shrink-0"></i>

        <div>
            <p class="font-medium">
                Atención
            </p>

            <p class="mt-1 text-sm">
                {{ session('warning') }}
            </p>
        </div>
    </div>
@endif
