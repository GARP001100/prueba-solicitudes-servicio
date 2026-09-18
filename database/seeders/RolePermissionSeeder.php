<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'access admin panel',
            'view users',
            'manage users',
            'view dashboard',
            'manage profile',
            'view appointments',
            'create appointments',
            'cancel own appointments',
            'view all appointments',
            'approve appointments',
            'manage doctors',
            'manage availability',
            'manage service requests',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('admin', 'web');
        $userRole = Role::findOrCreate('user', 'web');

        $adminRole->syncPermissions($permissions);

        $userRole->syncPermissions([
            'view dashboard',
            'manage profile',
            'view appointments',
            'create appointments',
            'cancel own appointments',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
