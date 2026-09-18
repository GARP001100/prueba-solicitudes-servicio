<x-app-layout>
    <x-slot name="header">
        Catálogo visual
    </x-slot>

    <div class="space-y-8">
        <section>
            <h2 class="text-xl font-bold text-slate-900">
                Tarjetas de indicadores
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Componentes reutilizables para dashboards.
            </p>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-admin.stat-card
                    title="Incidentes abiertos"
                    value="24"
                    icon="circle-alert"
                    description="4 requieren atención"
                />

                <x-admin.stat-card
                    title="En proceso"
                    value="12"
                    icon="loader-circle"
                    variant="info"
                    description="Asignados al equipo"
                />

                <x-admin.stat-card
                    title="Resueltos"
                    value="138"
                    icon="circle-check"
                    variant="success"
                    description="Durante el mes"
                />

                <x-admin.stat-card
                    title="Críticos"
                    value="3"
                    icon="triangle-alert"
                    variant="danger"
                    description="Prioridad inmediata"
                />
            </div>
        </section>

        <x-admin.card
            title="Estados y prioridades"
            subtitle="Badges reutilizables para la aplicación"
        >
            <div class="flex flex-wrap gap-2">
                <x-admin.badge>Neutral</x-admin.badge>
                <x-admin.badge variant="primary">Asignado</x-admin.badge>
                <x-admin.badge variant="info">En proceso</x-admin.badge>
                <x-admin.badge variant="success">Resuelto</x-admin.badge>
                <x-admin.badge variant="warning">Pendiente</x-admin.badge>
                <x-admin.badge variant="danger">Crítico</x-admin.badge>
            </div>
        </x-admin.card>

        <x-admin.card
            title="Controles de formulario"
            subtitle="Selectores avanzados y fechas"
        >
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label
                        for="responsible"
                        class="mb-1 block text-sm font-medium text-slate-700"
                    >
                        Responsable
                    </label>

                    <select
                        id="responsible"
                        data-tom-select
                        class="w-full"
                    >
                        <option value="">Seleccione una opción</option>
                        <option value="1">Ana Martínez</option>
                        <option value="2">Carlos Rodríguez</option>
                        <option value="3">María López</option>
                    </select>
                </div>

                <div>
                    <label
                        for="scheduled_at"
                        class="mb-1 block text-sm font-medium text-slate-700"
                    >
                        Fecha programada
                    </label>

                    <input
                        id="scheduled_at"
                        type="text"
                        data-datetimepicker
                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Seleccione fecha y hora"
                    >
                </div>
            </div>
        </x-admin.card>

        <x-admin.card
            title="Loader global"
            subtitle="Puede utilizarse en formularios y peticiones largas"
        >
            <button
                type="button"
                class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                x-on:click="
                    window.AdminUI.showLoader();
                    setTimeout(() => window.AdminUI.hideLoader(), 1800);
                "
            >
                Mostrar loader
            </button>
        </x-admin.card>
    </div>
</x-app-layout>
