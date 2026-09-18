@php
    $menuBaseClass = 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition';
    $menuActiveClass = 'bg-white text-blue-950 shadow-md';
    $menuInactiveClass = 'text-blue-100 hover:bg-white/10 hover:text-white';
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-blue-950 text-slate-200 shadow-2xl transition-transform duration-300 lg:translate-x-0"
    x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    aria-label="Navegación principal"
>
    <div class="flex h-16 items-center justify-between border-b border-white/10 px-5">
        <a
            href="{{ route('dashboard') }}"
            class="flex min-w-0 items-center gap-3"
        >
            <div class="relative flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white text-sm font-black text-blue-900 shadow-lg">
                <span>JP</span>
                <span
                    class="absolute bottom-0 h-1 w-full bg-amber-400"
                    aria-hidden="true"
                ></span>
            </div>

            <div class="min-w-0">
                <p class="truncate font-semibold leading-tight text-white">
                    {{ config('app.name') }}
                </p>
                <p class="mt-0.5 truncate text-xs text-blue-200">
                    Gestión institucional
                </p>
            </div>
        </a>

        <button
            type="button"
            class="rounded-xl p-2 text-blue-200 transition hover:bg-white/10 hover:text-white lg:hidden"
            x-on:click="sidebarOpen = false"
            aria-label="Cerrar menú"
        >
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
        <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-widest text-blue-300/70">
            Principal
        </p>

        <a
            href="{{ route('dashboard') }}"
            class="{{ $menuBaseClass }} {{ request()->routeIs('dashboard') ? $menuActiveClass : $menuInactiveClass }}"
        >
            <i data-lucide="layout-dashboard" class="h-5 w-5 shrink-0"></i>
            <span>Dashboard</span>
        </a>

        <a
            href="{{ route('ui-kit') }}"
            class="{{ $menuBaseClass }} {{ request()->routeIs('ui-kit') ? $menuActiveClass : $menuInactiveClass }}"
        >
            <i data-lucide="component" class="h-5 w-5 shrink-0"></i>
            <span>Catálogo visual</span>
        </a>

        <p class="mb-2 mt-7 px-3 text-xs font-semibold uppercase tracking-widest text-blue-300/70">
            Gestión
        </p>

        <a
            href="{{ route('service-requests.index') }}"
            class="{{ $menuBaseClass }} {{ request()->routeIs('service-requests.*') ? $menuActiveClass : $menuInactiveClass }}"
        >
            <i data-lucide="clipboard-list" class="h-5 w-5 shrink-0"></i>
            <span>Solicitudes de servicio</span>
        </a>

        {{--
            Módulo de agendamiento oculto temporalmente para la entrega.
            Las rutas, permisos, controladores y vistas se conservan sin cambios.

            @can('view appointments')
                <a
                    href="{{ route('doctors.index') }}"
                    class="{{ $menuBaseClass }} {{ request()->routeIs('doctors.*') ? $menuActiveClass : $menuInactiveClass }}"
                >
                    <i data-lucide="calendar-plus" class="h-5 w-5 shrink-0"></i>
                    <span>Agendar cita</span>
                </a>

                <a
                    href="{{ route('appointments.index') }}"
                    class="{{ $menuBaseClass }} {{ request()->routeIs('appointments.*') ? $menuActiveClass : $menuInactiveClass }}"
                >
                    <i data-lucide="calendar-days" class="h-5 w-5 shrink-0"></i>
                    <span>Mis citas</span>
                </a>
            @endcan

            @can('view all appointments')
                <a
                    href="{{ route('admin.appointments.index') }}"
                    class="{{ $menuBaseClass }} {{ request()->routeIs('admin.appointments.*') ? $menuActiveClass : $menuInactiveClass }}"
                >
                    <i data-lucide="calendar-check" class="h-5 w-5 shrink-0"></i>
                    <span>Aprobar citas</span>
                </a>
            @endcan
        --}}

        @can('manage users')
            <p class="mb-2 mt-7 px-3 text-xs font-semibold uppercase tracking-widest text-blue-300/70">
                Administración
            </p>

            <a
                href="{{ route('admin.crud.home') }}"
                class="{{ $menuBaseClass }} {{ request()->routeIs('admin.crud.*') ? $menuActiveClass : $menuInactiveClass }}"
            >
                <i data-lucide="panels-top-left" class="h-5 w-5 shrink-0"></i>
                <span>Centro de administración</span>
            </a>
        @endcan

        <p class="mb-2 mt-7 px-3 text-xs font-semibold uppercase tracking-widest text-blue-300/70">
            Cuenta
        </p>

        <a
            href="{{ route('profile.edit') }}"
            class="{{ $menuBaseClass }} {{ request()->routeIs('profile.edit') ? $menuActiveClass : $menuInactiveClass }}"
        >
            <i data-lucide="user-round" class="h-5 w-5 shrink-0"></i>
            <span>Mi perfil</span>
        </a>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="mb-3 flex items-center gap-3 rounded-xl bg-black/15 p-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-400 font-bold text-blue-950">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>

            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">
                    {{ auth()->user()->name ?? 'Usuario' }}
                </p>
                <p class="truncate text-xs text-blue-200">
                    {{ auth()->user()->email ?? '' }}
                </p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('logout') }}"
            data-loading-form
            data-loading-title="Cerrando sesión"
            data-loading-message="Estamos finalizando tu sesión de forma segura."
        >
            @csrf

            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 px-3 py-2.5 text-sm font-semibold text-blue-100 transition hover:border-red-300/40 hover:bg-red-500/10 hover:text-red-100"
            >
                <i data-lucide="log-out" class="h-4 w-4"></i>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>
