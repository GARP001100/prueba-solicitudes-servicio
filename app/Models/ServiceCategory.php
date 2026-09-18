<?php

namespace App\Models;

use Database\Factories\ServiceCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

class ServiceCategory extends Model
{
    /** @use HasFactory<ServiceCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function crudEnabled(): bool
    {
        return true;
    }

    public function institutionalServices(): HasMany
    {
        return $this->hasMany(InstitutionalService::class);
    }

    public function getDescription(): string
    {
        return (string) $this->name;
    }

    public function crudTitle(): string
    {
        return 'Categorías de servicios';
    }

    public function crudSingular(): string
    {
        return 'categoría';
    }

    public function crudIcon(): string
    {
        return 'folder';
    }

    public function crudFields(): array
    {
        return [
            'name',
            'description',
            'is_active',
        ];
    }

    public function crudListFields(): array
    {
        return [
            'id',
            'name',
            'description',
            'services_count',
            'is_active',
        ];
    }

    public function crudLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'services_count' => 'Servicios',
            'is_active' => 'Activo',
        ];
    }

    public function crudSearchable(): array
    {
        return [
            'name',
            'description',
        ];
    }

    public function crudRules(?int $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_categories', 'name')->ignore($id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function getIsActiveSelect(): array
    {
        return [
            1 => 'Activo',
            0 => 'Inactivo',
        ];
    }

    public function getServicesCountValue(): int
    {
        return $this->institutionalServices()->count();
    }
}
