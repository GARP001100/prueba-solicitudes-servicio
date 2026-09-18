<x-guest-layout>
    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">
            <i data-lucide="user-plus" class="h-4 w-4"></i>
            Registro de usuario
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Crear una cuenta
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Completa la información para acceder a la plataforma.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('register') }}"
        data-loading-form
        data-loading-title="Creando cuenta"
        data-loading-message="Estamos registrando la información suministrada."
        class="space-y-5"
    >
        @csrf

        <div>
            <x-input-label for="name" value="Nombre completo" />
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Escribe tu nombre"
                class="mt-1 block w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-700 focus:ring-blue-700"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="usuario@ejemplo.com"
                class="mt-1 block w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-700 focus:ring-blue-700"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ visible: false }">
            <x-input-label for="password" value="Contraseña" />

            <div class="relative mt-1">
                <input
                    id="password"
                    x-bind:type="visible ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    class="block w-full rounded-xl border-slate-300 py-2.5 pl-3 pr-12 text-sm shadow-sm transition focus:border-blue-700 focus:ring-blue-700"
                >
                <button
                    type="button"
                    x-on:click="visible = ! visible"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-blue-700"
                    aria-label="Mostrar u ocultar contraseña"
                >
                    <i x-show="! visible" data-lucide="eye" class="h-4 w-4"></i>
                    <i x-show="visible" data-lucide="eye-off" class="h-4 w-4" style="display: none"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div x-data="{ visible: false }">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />

            <div class="relative mt-1">
                <input
                    id="password_confirmation"
                    x-bind:type="visible ? 'text' : 'password'"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repite la contraseña"
                    class="block w-full rounded-xl border-slate-300 py-2.5 pl-3 pr-12 text-sm shadow-sm transition focus:border-blue-700 focus:ring-blue-700"
                >
                <button
                    type="button"
                    x-on:click="visible = ! visible"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-blue-700"
                    aria-label="Mostrar u ocultar confirmación"
                >
                    <i x-show="! visible" data-lucide="eye" class="h-4 w-4"></i>
                    <i x-show="visible" data-lucide="eye-off" class="h-4 w-4" style="display: none"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-800 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
        >
            <i data-lucide="user-plus" class="h-4 w-4"></i>
            Crear cuenta
        </button>

        <p class="text-center text-sm text-slate-500">
            ¿Ya tienes una cuenta?
            <a
                href="{{ route('login') }}"
                class="font-semibold text-blue-700 transition hover:text-blue-900"
            >
                Inicia sesión
            </a>
        </p>
    </form>
</x-guest-layout>
