<x-app-layout>
    <x-slot name="header">
        Mis citas
    </x-slot>

    <div class="space-y-6" data-user-appointments>
        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Mis citas</h2>
                <p class="text-sm text-slate-600">Consulta tus solicitudes, aprobaciones y rechazos.</p>
            </div>
            <a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                <i data-lucide="calendar-plus" class="h-4 w-4"></i>
                Agendar nueva cita
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-amber-700">Pendientes</p><p class="mt-2 text-2xl font-bold text-amber-800">0</p></div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-emerald-700">Aprobadas</p><p class="mt-2 text-2xl font-bold text-emerald-800">0</p></div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-rose-700">Rechazadas</p><p class="mt-2 text-2xl font-bold text-rose-800">0</p></div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-slate-700">Próxima cita</p><p class="mt-2 text-sm font-semibold text-slate-800">Sin citas</p></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div data-appointment-calendar class="min-h-[500px]"></div>
        </div>
    </div>
</x-app-layout>
