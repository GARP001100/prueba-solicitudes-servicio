<x-app-layout>
    <x-slot name="header">
        {{ $doctor->full_name }}
    </x-slot>

    <div class="space-y-6" data-doctor-calendar data-doctor-id="{{ $doctor->id }}" data-doctor-name="{{ $doctor->full_name }}">
        <div class="grid gap-6 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-3">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Especialista</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $doctor->full_name }}</h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Disponible</span>
                </div>

                <p class="text-sm text-slate-600">{{ $doctor->professional_title }} · {{ $doctor->specialty }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $doctor->bio ?? 'Experto en atención odontológica integral.' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ubicación</p>
                <p class="mt-3 text-sm font-medium text-slate-800">Consultorio {{ $doctor->phone ?? '201' }}</p>
                <a href="{{ route('doctors.index') }}" class="mt-5 inline-flex items-center rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-blue-400 hover:text-blue-700">
                    Volver al directorio
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-2 py-1 text-emerald-700"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Disponible</span>
                <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-2 py-1 text-amber-700"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> Pendiente</span>
                <span class="inline-flex items-center gap-2 rounded-full bg-rose-100 px-2 py-1 text-rose-700"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> No disponible</span>
            </div>
            <div data-doctor-calendar data-doctor-id="{{ $doctor->id }}" class="min-h-[520px]"></div>
        </div>
    </div>

    <div id="appointment-drawer" class="fixed inset-y-0 right-0 z-50 hidden w-full max-w-md border-l border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h3 class="text-lg font-semibold text-slate-900">Solicitar cita</h3>
            <button type="button" data-close-drawer class="text-slate-500 hover:text-slate-700">Cerrar</button>
        </div>

        <form id="appointment-form" class="space-y-4 p-5">
            @csrf
            <input type="hidden" name="doctor_profile_id" id="doctor_profile_id" value="{{ $doctor->id }}">
            <input type="hidden" name="starts_at" id="appointment-starts_at">

            <div>
                <label for="service_id" class="mb-2 block text-sm font-medium text-slate-700">Servicio</label>
                <select id="service_id" name="institutional_service_id" data-tom-select class="w-full">
                    <option value="">Seleccione un servicio</option>
                    @foreach (\App\Models\InstitutionalService::query()->where('is_active', true)->get() as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="user_notes" class="mb-2 block text-sm font-medium text-slate-700">Observación general</label>
                <textarea id="user_notes" name="user_notes" rows="4" class="w-full rounded-xl border border-slate-300 p-3 text-sm" placeholder="Cuéntenos la necesidad de la cita"></textarea>
            </div>

            <button type="submit" class="w-full rounded-xl bg-blue-700 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-800">
                Solicitar cita
            </button>
        </form>
    </div>
</x-app-layout>
