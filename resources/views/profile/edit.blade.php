<x-app-layout>
    <x-slot name="header">Mi perfil</x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <section class="rounded-3xl bg-gradient-to-r from-blue-950 to-blue-800 p-6 text-white shadow-xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-400 text-2xl font-bold text-blue-950">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ auth()->user()->name }}</h2>
                    <p class="mt-1 text-blue-100/80">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6">
            <x-admin.card title="Información personal" subtitle="Actualiza el nombre y correo electrónico asociados a tu cuenta.">
                @include('profile.partials.update-profile-information-form')
            </x-admin.card>

            <x-admin.card title="Seguridad" subtitle="Utiliza una contraseña larga y difícil de adivinar.">
                @include('profile.partials.update-password-form')
            </x-admin.card>

            <section class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm">
                <header class="border-b border-red-100 bg-red-50 px-5 py-4">
                    <h2 class="font-semibold text-red-900">Zona de riesgo</h2>
                    <p class="mt-1 text-sm text-red-700">La eliminación de la cuenta es permanente.</p>
                </header>
                <div class="p-5">
                    @include('profile.partials.delete-user-form')
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
