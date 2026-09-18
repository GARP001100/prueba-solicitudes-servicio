<section class="space-y-4">
    <p class="text-sm leading-6 text-slate-600">
        Al eliminar la cuenta se borrarán permanentemente sus datos asociados. Esta acción no se puede deshacer.
    </p>

    <x-danger-button x-data x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Eliminar cuenta
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="p-6" data-loading-form>
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-semibold text-slate-900">¿Confirmas la eliminación de tu cuenta?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Ingresa tu contraseña para confirmar esta acción permanente.</p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="Contraseña" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-danger-button>Eliminar definitivamente</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
