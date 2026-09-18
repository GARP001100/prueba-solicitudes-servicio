<x-app-layout>
    <x-slot name="header">
        Profesionales disponibles
    </x-slot>

    <div class="space-y-6" data-doctor-directory>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Profesionales activos</h2>
                    <p class="text-sm text-slate-600">Consulta la agenda de cada especialista y selecciona la franja disponible.</p>
                </div>
                <div class="w-full max-w-md">
                    <label for="doctor-search" class="sr-only">Buscar profesional</label>
                    <input id="doctor-search" type="search" placeholder="Buscar por nombre o especialidad" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($doctors as $doctor)
                <article class="doctor-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-md" data-doctor-name="{{ strtolower($doctor->full_name) }}" data-doctor-specialty="{{ strtolower($doctor->specialty ?? '') }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-sm font-bold text-blue-700">
                                {{ strtoupper(substr($doctor->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Doctor</p>
                                <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $doctor->full_name }}</h2>
                            </div>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Activo</span>
                    </div>

                    <p class="mt-3 text-sm font-medium text-slate-700">{{ $doctor->professional_title }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $doctor->specialty }}</p>

                    <div class="mt-4 rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
                        <p class="font-medium text-slate-700">Biografía</p>
                        <p class="mt-2 line-clamp-3">{{ Str::limit($doctor->bio ?? 'Experto en atención odontológica integral.', 120) }}</p>
                    </div>

                    <div class="mt-4 text-sm text-slate-600">
                        @foreach ($doctor->availabilities->take(2) as $availability)
                            <p>{{ ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'][$availability->day_of_week] }} · {{ $availability->starts_at }} - {{ $availability->ends_at }}</p>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('doctors.show', $doctor) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                            <i data-lucide="calendar-plus" class="h-4 w-4"></i>
                            Consultar disponibilidad
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 md:col-span-2 xl:col-span-3">
                    No hay profesionales activos en este momento.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
