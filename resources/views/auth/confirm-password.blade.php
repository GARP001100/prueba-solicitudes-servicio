<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-slate-900">Confirma tu contraseña</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Esta es un área segura. Confirma tu contraseña antes de continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5" data-loading-form>
        @csrf
        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <button type="submit" class="w-full rounded-xl bg-blue-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-900">Confirmar contraseña</button>
    </form>
</x-guest-layout>
