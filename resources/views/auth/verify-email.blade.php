<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-slate-900">Verifica tu correo</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Enviamos un enlace de verificación a tu correo. Ábrelo para completar el registro.</p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">Se envió un nuevo enlace de verificación.</div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}" data-loading-form>
            @csrf
            <button type="submit" class="w-full rounded-xl bg-blue-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-900">Reenviar correo de verificación</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" data-loading-form>
            @csrf
            <button type="submit" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cerrar sesión</button>
        </form>
    </div>
</x-guest-layout>
