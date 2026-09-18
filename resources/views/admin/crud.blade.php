<x-app-layout>
    <x-slot name="header">Centro CRUD administrativo</x-slot>

    <div data-dynamic-crud data-resource="{{ $metadata['resource'] }}" class="grid gap-6 lg:grid-cols-[17rem_minmax(0,1fr)]">
        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:sticky lg:top-24">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-800">
                    <i data-lucide="panels-top-left" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Recursos</p>
                    <p class="text-xs text-slate-500">Selecciona qué administrar</p>
                </div>
            </div>

            <nav class="mt-3 space-y-1">
                @foreach ($metadata['resources'] as $item)
                    <a
                        href="{{ route('admin.crud.index', ['resource' => $item['resource']]) }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-blue-950 text-white shadow-md' => $metadata['resource'] === $item['resource'],
                            'text-slate-600 hover:bg-blue-50 hover:text-blue-900' => $metadata['resource'] !== $item['resource'],
                        ])
                    >
                        <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5 shrink-0"></i>
                        <span class="truncate">{{ $item['title'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="min-w-0 space-y-6">
            <section class="rounded-3xl bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 p-6 text-white shadow-xl sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm font-semibold uppercase tracking-widest text-blue-200">
                            <i data-lucide="{{ $metadata['icon'] }}" class="h-5 w-5"></i>
                            CRUD dinámico
                        </div>
                        <h2 class="mt-3 text-2xl font-bold sm:text-3xl">{{ $metadata['title'] }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100/85">Formulario, listado, búsqueda y paginación generados desde el modelo.</p>
                    </div>

                    <button type="button" data-crud-new class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-blue-950 shadow-lg transition hover:bg-amber-300">
                        <i data-lucide="plus" class="h-5 w-5"></i>
                        <span data-crud-new-label>Nuevo {{ $metadata['singular'] }}</span>
                    </button>
                </div>
            </section>

            <x-admin.card :padding="false">
                <div class="flex flex-col gap-4 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full max-w-md">
                        <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                        <input type="search" data-crud-search class="w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-3 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700" placeholder="Buscar registros...">
                    </div>
                    <p data-crud-summary class="text-sm text-slate-500">Cargando registros...</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead data-crud-head class="bg-slate-50"></thead>
                        <tbody data-crud-body class="divide-y divide-slate-100 bg-white"></tbody>
                    </table>
                </div>

                <div data-crud-pagination class="flex flex-col gap-3 border-t border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between"></div>
            </x-admin.card>
        </div>

        <div data-crud-overlay class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm" aria-hidden="true"></div>

        <aside data-crud-drawer class="fixed inset-y-0 right-0 z-50 flex w-full max-w-lg translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300" aria-label="Formulario dinámico">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">CRUD dinámico</p>
                    <h2 data-crud-form-title class="mt-1 text-xl font-bold text-slate-900">Nuevo registro</h2>
                </div>
                <button type="button" data-crud-close class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100" aria-label="Cerrar formulario">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form data-crud-form class="flex min-h-0 flex-1 flex-col">
                <div data-crud-fields class="flex-1 space-y-5 overflow-y-auto p-5"></div>
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 p-5">
                    <button type="button" data-crud-close class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancelar</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </aside>
    </div>
</x-app-layout>
