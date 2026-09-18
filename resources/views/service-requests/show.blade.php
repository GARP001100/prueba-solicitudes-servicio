<x-app-layout>
    <x-slot name="header">
        Detalle de solicitud
    </x-slot>

    @php
        $statusClasses = [
            \App\Models\ServiceRequest::STATUS_NEW => 'bg-blue-100 text-blue-800',
            \App\Models\ServiceRequest::STATUS_IN_PROGRESS => 'bg-amber-100 text-amber-800',
            \App\Models\ServiceRequest::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800',
        ];
        $canManageServiceRequests = auth()->user()->can('manage service requests');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('service-requests.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Volver al listado
                </a>
                <h1 class="mt-3 text-2xl font-bold text-slate-900">Solicitud #{{ $serviceRequest->id }}</h1>
            </div>

            <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold {{ $statusClasses[$serviceRequest->status] ?? 'bg-slate-100 text-slate-700' }}">
                {{ $statuses[$serviceRequest->status] ?? $serviceRequest->status }}
            </span>
        </div>

        <div @class([
            'grid gap-6',
            'lg:grid-cols-[minmax(0,1fr)_22rem]' => $canManageServiceRequests,
        ])>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Información de la solicitud</h2>

                <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Solicitante</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">{{ $serviceRequest->requester_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Correo electrónico</dt>
                        <dd class="mt-1 break-all text-sm text-slate-900">{{ $serviceRequest->requester_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tipo</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $types[$serviceRequest->request_type] ?? $serviceRequest->request_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Fecha de creación</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Última actualización</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $serviceRequest->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Descripción</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $serviceRequest->description }}</p>
                </div>

                @unless ($canManageServiceRequests)
                    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        El estado de esta solicitud es administrado por el equipo responsable.
                    </div>
                @endunless
            </section>

            @if ($canManageServiceRequests)
                <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-800">
                            <i data-lucide="refresh-cw" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900">Actualizar estado</h2>
                            <p class="text-xs text-slate-500">Acción disponible para administradores.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('service-requests.update-status', $serviceRequest) }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                            <select id="status" name="status" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-700 focus:ring-blue-700">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $serviceRequest->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Actualizar estado
                        </button>
                    </form>
                </aside>
            @endif
        </div>
    </div>
</x-app-layout>
