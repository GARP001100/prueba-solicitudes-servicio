<x-app-layout>
    <x-slot name="header">
        Panel administrativo
    </x-slot>

    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 p-6 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm font-semibold uppercase tracking-widest text-blue-200">
                        <i data-lucide="shield-check" class="h-5 w-5"></i>
                        Administración
                    </div>

                    <h2 class="mt-3 text-2xl font-bold sm:text-3xl">
                        Hola, {{ auth()->user()->name }}
                    </h2>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100/85 sm:text-base">
                        Desde este espacio puedes supervisar usuarios, roles, permisos y la configuración general del sistema.
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400 text-blue-950">
                        <i data-lucide="crown" class="h-6 w-6"></i>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-blue-200">Rol activo</p>
                        <p class="font-semibold text-white">Administrador</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card
                title="Usuarios"
                :value="\App\Models\User::count()"
                icon="users-round"
                description="Registrados en el sistema"
            />

            <x-admin.stat-card
                title="Citas pendientes"
                :value="\App\Models\Appointment::query()->where('status', \App\Models\Appointment::STATUS_PENDING)->count()"
                icon="calendar-clock"
                variant="warning"
                description="Requieren revisión"
            />

            <x-admin.stat-card
                title="Doctores activos"
                :value="\App\Models\DoctorProfile::query()->where('is_active', true)->count()"
                icon="stethoscope"
                variant="info"
                description="Especialistas disponibles"
            />

            <x-admin.stat-card
                title="Entorno"
                :value="app()->environment()"
                icon="server-cog"
                variant="warning"
                description="Configuración activa"
            />
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <x-admin.card
                title="Funciones administrativas"
                subtitle="Accesos principales del rol administrador"
                class="xl:col-span-2"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <article class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:bg-blue-50/60">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-800 transition group-hover:bg-blue-800 group-hover:text-white">
                            <i data-lucide="users-round" class="h-5 w-5"></i>
                        </div>

                        <h3 class="mt-4 font-semibold text-slate-900">
                            Gestión de usuarios
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Espacio preparado para consultar usuarios y administrar sus roles.
                        </p>

                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-800">
                            Próximo módulo
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </div>
                    </article>

                    <a
                        href="{{ route('ui-kit') }}"
                        class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-amber-300 hover:bg-amber-50/60"
                    >
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-800 transition group-hover:bg-amber-400 group-hover:text-blue-950">
                            <i data-lucide="component" class="h-5 w-5"></i>
                        </div>

                        <h3 class="mt-4 font-semibold text-slate-900">
                            Catálogo visual
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Consulta componentes, formularios y elementos reutilizables.
                        </p>

                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-amber-800">
                            Abrir catálogo
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </div>
                    </a>
                </div>
            </x-admin.card>

            <x-admin.card
                title="Estado del sistema"
                subtitle="Resumen de servicios"
            >
                <dl class="space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="activity" class="h-4 w-4"></i>
                            Aplicación
                        </dt>
                        <dd><x-admin.badge variant="success">Operativa</x-admin.badge></dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="database" class="h-4 w-4"></i>
                            Base de datos
                        </dt>
                        <dd><x-admin.badge variant="info">SQLite</x-admin.badge></dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="list-restart" class="h-4 w-4"></i>
                            Cola
                        </dt>
                        <dd><x-admin.badge variant="primary">Database</x-admin.badge></dd>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <dt class="flex items-center gap-2 text-slate-500">
                            <i data-lucide="server-cog" class="h-4 w-4"></i>
                            Entorno
                        </dt>
                        <dd><x-admin.badge variant="warning">{{ app()->environment() }}</x-admin.badge></dd>
                    </div>
                </dl>
            </x-admin.card>
        </section>
    </div>
</x-app-layout>
