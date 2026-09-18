<x-app-layout>
    <x-slot name="header">
        Aprobación de citas odontológicas
    </x-slot>

    <div class="space-y-6" data-admin-appointments>
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-rose-700">Conflictos pendientes</p><p class="mt-2 text-2xl font-bold text-rose-800">0</p></div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-amber-700">Solicitudes pendientes</p><p class="mt-2 text-2xl font-bold text-amber-800">0</p></div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-emerald-700">Citas aprobadas hoy</p><p class="mt-2 text-2xl font-bold text-emerald-800">0</p></div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs uppercase tracking-[0.2em] text-slate-700">Rechazadas</p><p class="mt-2 text-2xl font-bold text-slate-800">0</p></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Conflictos por resolver</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach (\App\Models\Appointment::query()->where('status', \App\Models\Appointment::STATUS_PENDING)->with(['doctorProfile', 'institutionalService'])->get() as $appointment)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-semibold text-amber-800">{{ $appointment->doctorProfile?->full_name }}</p>
                        <p class="mt-2 text-xs text-amber-700">{{ $appointment->starts_at?->format('d/m/Y H:i') }} · {{ $appointment->institutionalService?->name }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div data-admin-appointment-calendar class="min-h-[500px]"></div>
        </div>
    </div>
</x-app-layout>
