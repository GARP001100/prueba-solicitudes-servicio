<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-slate-900">Recuperar contraseña</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Escribe tu correo y te enviaremos un enlace para crear una nueva contraseña.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" data-loading-form>
        @csrf
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-xl bg-blue-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-900">Enviar enlace de recuperación</button>
        <p class="text-center text-sm"><a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-900">Volver al inicio de sesión</a></p>
    </form>
</x-guest-layout>
