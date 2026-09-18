<x-guest-layout>
    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 ring-1 ring-blue-200">
            <i data-lucide="shield-check" class="h-4 w-4"></i>
            Acceso seguro
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Bienvenido nuevamente
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Ingresa tus credenciales para acceder a la plataforma.
        </p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form
        method="POST"
        action="{{ route('login') }}"
        data-loading-form
        data-loading-title="Validando acceso"
        data-loading-message="Estamos verificando tus credenciales."
        class="space-y-5"
    >
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />

            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
                </div>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="usuario@ejemplo.com"
                    class="block w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-700 focus:ring-blue-700"
                >
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <x-input-label for="password" value="Contraseña" />

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm font-semibold text-blue-700 transition hover:text-blue-900"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <div x-data="{ visible: false }" class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i data-lucide="lock-keyhole" class="h-4 w-4 text-slate-400"></i>
                </div>

                <input
                    id="password"
                    x-bind:type="visible ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Ingresa tu contraseña"
                    class="block w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-12 text-sm shadow-sm transition focus:border-blue-700 focus:ring-blue-700"
                >

                <button
                    type="button"
                    x-on:click="visible = ! visible"
                    x-bind:aria-label="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-blue-700"
                >
                    <i x-show="! visible" data-lucide="eye" class="h-4 w-4"></i>
                    <i x-show="visible" data-lucide="eye-off" class="h-4 w-4" style="display: none"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label class="flex cursor-pointer items-center gap-2.5">
            <input
                type="checkbox"
                name="remember"
                class="rounded border-slate-300 text-blue-700 shadow-sm focus:ring-blue-700"
            >

            <span class="text-sm text-slate-600">Mantener sesión iniciada</span>
        </label>

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-800 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
        >
            <i data-lucide="log-in" class="h-4 w-4"></i>
            Iniciar sesión
        </button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500">
                ¿No tienes una cuenta?
                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-blue-700 transition hover:text-blue-900"
                >
                    Regístrate
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
