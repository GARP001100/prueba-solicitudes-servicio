<x-app-layout>
    <x-slot name="header">
        Nueva solicitud de servicio
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('service-requests.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Volver al listado
            </a>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h1 class="text-xl font-bold text-slate-900">Nueva solicitud de servicio</h1>
                <p class="mt-1 text-sm text-slate-500">Completa la información requerida. La solicitud iniciará con estado Nueva.</p>
            </div>

            <form method="POST" action="{{ route('service-requests.store') }}" class="space-y-6 p-6" novalidate>
                @csrf

                <div>
                    <label for="requester_name" class="mb-1 block text-sm font-medium text-slate-700">Nombre del solicitante</label>
                    <input id="requester_name" name="requester_name" type="text" value="{{ old('requester_name') }}" maxlength="150" required autofocus class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-700 focus:ring-blue-700">
                    @error('requester_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="requester_email" class="mb-1 block text-sm font-medium text-slate-700">Correo electrónico</label>
                    <input id="requester_email" name="requester_email" type="email" value="{{ old('requester_email') }}" maxlength="255" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-700 focus:ring-blue-700">
                    @error('requester_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="request_type" class="mb-1 block text-sm font-medium text-slate-700">Tipo de solicitud</label>
                    <select id="request_type" name="request_type" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-700 focus:ring-blue-700">
                        <option value="">Selecciona una opción</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('request_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('request_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Descripción</label>
                    <textarea id="description" name="description" rows="7" maxlength="3000" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-700 focus:ring-blue-700" placeholder="Describe con claridad la necesidad o situación presentada.">{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">Entre 10 y 3000 caracteres.</p>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('service-requests.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        Registrar solicitud
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
