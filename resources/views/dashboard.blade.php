<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="space-y-8">
        <section class="rounded-3xl bg-gradient-to-r from-blue-950 to-blue-800 p-6 text-white shadow-xl sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-blue-200">Resumen general</p>
            <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Hola, {{ auth()->user()->name }}</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100/80 sm:text-base">
                Este dashboard es una base visual. Los valores se reemplazarán por datos reales cuando se implemente el primer módulo.
            </p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card title="Solicitudes" value="24" icon="inbox" description="Pendientes de revisión" />
            <x-admin.stat-card title="En proceso" value="12" icon="loader-circle" variant="info" description="Asignadas actualmente" />
            <x-admin.stat-card title="Resueltas" value="138" icon="circle-check" variant="success" description="Durante el mes" />
            <x-admin.stat-card title="Críticas" value="3" icon="triangle-alert" variant="danger" description="Requieren atención" />
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <x-admin.card title="Accesos rápidos" subtitle="Funciones disponibles en el esqueleto" class="xl:col-span-2">
                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-800">
                            <i data-lucide="user-round" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Mi perfil</p>
                            <p class="text-sm text-slate-500">Actualizar información y contraseña</p>
                        </div>
                    </a>

                    <a href="{{ route('ui-kit') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-amber-300 hover:bg-amber-50">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                            <i data-lucide="component" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Catálogo visual</p>
                            <p class="text-sm text-slate-500">Consultar componentes reutilizables</p>
                        </div>
                    </a>
                </div>
            </x-admin.card>

            <x-admin.card title="Estado del entorno" subtitle="Servicios principales">
                <dl class="space-y-4 text-sm">
                    @foreach ([
                        ['name' => 'Aplicación', 'value' => 'Operativa', 'variant' => 'success'],
                        ['name' => 'Base de datos', 'value' => 'SQLite', 'variant' => 'info'],
                        ['name' => 'Cola', 'value' => 'Database', 'variant' => 'primary'],
                        ['name' => 'Entorno', 'value' => app()->environment(), 'variant' => 'warning'],
                    ] as $item)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-slate-500">{{ $item['name'] }}</dt>
                            <dd><x-admin.badge :variant="$item['variant']">{{ $item['value'] }}</x-admin.badge></dd>
                        </div>
                    @endforeach
                </dl>
            </x-admin.card>
        </section>
    </div>
</x-app-layout>
