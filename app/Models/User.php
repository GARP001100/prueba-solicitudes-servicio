<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function crudTitle(): string
    {
        return 'Gestión de usuarios';
    }

    public function crudFields(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
        ];
    }

    public function crudListFields(): array
    {
        return [
            'id',
            'name',
            'email',
            'role',
            'created_at',
        ];
    }

    public function crudLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre completo',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'role' => 'Rol',
            'created_at' => 'Fecha de registro',
        ];
    }

    public function crudSearchable(): array
    {
        return [
            'name',
            'email',
        ];
    }

    public function crudRules(?int $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($id),
            ],
            'password' => $id
                ? [
                    'nullable',
                    'string',
                    'min:8',
                ]
                : [
                    'required',
                    'string',
                    'min:8',
                ],
            'role' => [
                'required',
                Rule::exists('roles', 'name')
                    ->where(
                        fn ($query) => $query->where(
                            'guard_name',
                            'web',
                        ),
                    ),
            ],
        ];
    }

    public function getRoleSelect(): array
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }
}
