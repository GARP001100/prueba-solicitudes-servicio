<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [
    'resources' => [
        'users' => [
            'model' => User::class,
            'title' => 'Gestión de usuarios',
            'singular' => 'usuario',
            'icon' => 'users-round',
            'permission' => 'manage users',
            'virtual' => ['role'],
        ],

        'roles' => [
            'model' => Role::class,
            'title' => 'Gestión de roles',
            'singular' => 'rol',
            'icon' => 'shield-check',
            'permission' => 'manage users',
            'fields' => ['name', 'permissions'],
            'list_fields' => ['id', 'name', 'permissions', 'created_at'],
            'searchable' => ['name'],
            'virtual' => ['permissions'],
            'labels' => [
                'id' => 'ID',
                'name' => 'Nombre',
                'permissions' => 'Permisos',
                'created_at' => 'Fecha de creación',
            ],
            'rules' => [
                'name' => ['required', 'string', 'max:100'],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['string', 'exists:permissions,name'],
            ],
            'protected_names' => ['admin', 'user'],
            'defaults' => ['guard_name' => 'web'],
        ],

        'permissions' => [
            'model' => Permission::class,
            'title' => 'Gestión de permisos',
            'singular' => 'permiso',
            'icon' => 'key-round',
            'permission' => 'manage users',
            'fields' => ['name'],
            'list_fields' => ['id', 'name', 'roles', 'created_at'],
            'searchable' => ['name'],
            'virtual' => ['roles'],
            'labels' => [
                'id' => 'ID',
                'name' => 'Nombre',
                'roles' => 'Roles asignados',
                'created_at' => 'Fecha de creación',
            ],
            'rules' => [
                'name' => ['required', 'string', 'max:100'],
            ],
            'protected_names' => ['manage users'],
            'defaults' => ['guard_name' => 'web'],
        ],
    ],

    'per_page' => 10,
];
