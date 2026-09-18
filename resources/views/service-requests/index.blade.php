<x-app-layout>
    <x-slot name="header">
        Solicitudes de servicio
    </x-slot>

    @php
        $statusClasses = [
            \App\Models\ServiceRequest::STATUS_NEW => 'bg-blue-100 text-blue-800',
            \App\Models\ServiceRequest::STATUS_IN_PROGRESS => 'bg-amber-100 text-amber-800',
            \App\Models\ServiceRequest::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800',
        ];
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-3xl bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 p-6 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-blue-200">Gestión institucional</p>
                    <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Solicitudes de servicio</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100">Registra, consulta y gestiona el estado de las solicitudes recibidas.</p>
                </div>

                <a href="{{ route('service-requests.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-blue-950 shadow-lg transition hover:bg-amber-300">
                    <i data-lucide="plus" class="h-5 w-5"></i>
                    Nueva solicitud
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('service-requests.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(0,2fr)_1fr_1fr_auto]">
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-slate-700">Buscar</label>
                    <input id="search" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Nombre, correo o descripción" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                </div>

                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                    <select id="status" name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        <option value="">Todos</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="request_type" class="mb-1 block text-sm font-medium text-slate-700">Tipo</label>
                    <select id="request_type" name="request_type" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        <option value="">Todos</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected($filters['request_type'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-900">Filtrar</button>
                    <a href="{{ route('service-requests.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Limpiar</a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Solicitudes registradas</h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-600">{{ $serviceRequests->total() }} en total</span>
            </div>

            @if ($serviceRequests->isEmpty())
                <div class="px-6 py-16 text-center">
                    <i data-lucide="inbox" class="mx-auto h-10 w-10 text-slate-400"></i>
                    <h3 class="mt-4 font-semibold text-slate-900">No hay solicitudes registradas con los criterios seleccionados.</h3>
                    <a href="{{ route('service-requests.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">Registrar solicitud</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                @foreach (['ID', 'Solicitante', 'Correo electrónico', 'Tipo', 'Estado', 'Fecha de creación', 'Acción'] as $heading)
                                    <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $heading }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($serviceRequests as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4 text-sm font-semibold text-slate-900">#{{ $item->id }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $item->requester_name }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $item->requester_email }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $types[$item->request_type] ?? $item->request_type }}</td>
                                    <td class="px-5 py-4 text-sm">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$item->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $statuses[$item->status] ?? $item->status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-4 text-sm">
                                        <a href="{{ route('service-requests.show', $item) }}" class="font-semibold text-blue-700 hover:text-blue-900">Ver detalle</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $serviceRequests->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
