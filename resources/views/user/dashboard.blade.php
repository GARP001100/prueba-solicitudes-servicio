<x-app-layout>
    <x-slot name="header">
        Mi espacio
    </x-slot>

    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 p-6 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm font-semibold uppercase tracking-widest text-blue-200">
                        <i data-lucide="circle-user-round" class="h-5 w-5"></i>
                        Portal de usuario
                    </div>

                    <h2 class="mt-3 text-2xl font-bold sm:text-3xl">
                        Bienvenido, {{ auth()->user()->name }}
                    </h2>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100/85 sm:text-base">
                        Consulta tu información y accede a las funciones disponibles para tu cuenta.
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400 text-blue-950">
                        <i data-lucide="user-round" class="h-6 w-6"></i>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-blue-200">Rol activo</p>
                        <p class="font-semibold text-white">
                            {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'user') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <x-admin.stat-card
                title="Citas pendientes"
                :value="\App\Models\Appointment::query()->where('user_id', auth()->id())->where('status', \App\Models\Appointment::STATUS_PENDING)->count()"
                icon="calendar-clock"
                description="Solicitudes en revisión"
            />

            <x-admin.stat-card
                title="Citas aprobadas"
                :value="\App\Models\Appointment::query()->where('user_id', auth()->id())->where('status', \App\Models\Appointment::STATUS_APPROVED)->count()"
                icon="calendar-check-2"
                variant="success"
                description="Confirmadas para atención"
            />

            <x-admin.stat-card
                title="Doctores activos"
                :value="\App\Models\DoctorProfile::query()->where('is_active', true)->count()"
                icon="stethoscope"
                variant="info"
                description="Especialistas disponibles"
            />
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <x-admin.card
                title="Mi perfil"
                subtitle="Información personal y seguridad"
            >
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-800">
                        <i data-lucide="user-round" class="h-6 w-6"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="mt-1 truncate text-sm text-slate-500">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>

                <p class="mt-5 text-sm leading-6 text-slate-600">
                    Actualiza tu nombre, correo electrónico o contraseña desde la configuración del perfil.
                </p>

                <a
                    href="{{ route('profile.edit') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-900"
                >
                    <i data-lucide="settings" class="h-4 w-4"></i>
                    Administrar perfil
                </a>
            </x-admin.card>

            <x-admin.card
                title="Estado de la cuenta"
                subtitle="Resumen del acceso actual"
            >
                <dl class="space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            Estado
                        </dt>
                        <dd><x-admin.badge variant="success">Activo</x-admin.badge></dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="shield" class="h-4 w-4"></i>
                            Rol
                        </dt>
                        <dd>
                            <x-admin.badge variant="primary">
                                {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'user') }}
                            </x-admin.badge>
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="mail" class="h-4 w-4"></i>
                            Correo
                        </dt>
                        <dd class="max-w-56 truncate font-medium text-slate-800">
                            {{ auth()->user()->email }}
                        </dd>
                    </div>
                </dl>
            </x-admin.card>
        </section>

        <x-admin.card
            title="Mis solicitudes"
            subtitle="Espacio preparado para el primer módulo funcional"
        >
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center sm:p-10">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-800">
                    <i data-lucide="inbox" class="h-7 w-7"></i>
                </div>

                <h3 class="mt-4 font-semibold text-slate-900">
                    No hay solicitudes registradas
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    Este espacio podrá utilizarse para incidentes, solicitudes, reservas o cualquier módulo requerido por la prueba técnica.
                </p>

                <button
                    type="button"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-800"
                    disabled
                >
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Nuevo registro
                </button>
            </div>
        </x-admin.card>
    </div>
</x-app-layout>
