<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class LocalDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $admin = User::updateOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Administrador',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin123*'),
            ],
        );

        $admin->syncRoles([
            'admin',
        ]);

        $user = User::updateOrCreate(
            [
                'email' => 'usuario@example.com',
            ],
            [
                'name' => 'Usuario de prueba',
                'email_verified_at' => now(),
                'password' => Hash::make('Usuario123*'),
            ],
        );

        $user->syncRoles([
            'user',
        ]);

        if (class_exists(CatalogSeeder::class)) {
            $this->call([
                CatalogSeeder::class,
            ]);
        }

        if (class_exists(DentalSchedulingSeeder::class)) {
            $this->call([
                DentalSchedulingSeeder::class,
            ]);
        }

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $this->command?->newLine();

        $this->command?->info(
            'Datos locales recuperados correctamente.',
        );

        $this->command?->table(
            [
                'Perfil',
                'Correo',
                'Contraseña',
                'Rol',
            ],
            [
                [
                    'Administrador',
                    'admin@example.com',
                    'Admin123*',
                    'admin',
                ],
                [
                    'Usuario',
                    'usuario@example.com',
                    'Usuario123*',
                    'user',
                ],
            ],
        );
    }
}
