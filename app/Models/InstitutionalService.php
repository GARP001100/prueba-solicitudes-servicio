<?php

namespace App\Models;

use Database\Factories\InstitutionalServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

class InstitutionalService extends Model
{
    /** @use HasFactory<InstitutionalServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'description',
        'duration_minutes',
        'requires_approval',
        'is_active',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function crudEnabled(): bool
    {
        return true;
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function getDescription(): string
    {
        return (string) $this->name;
    }

    public function crudTitle(): string
    {
        return 'Servicios institucionales';
    }

    public function crudSingular(): string
    {
        return 'servicio';
    }

    public function crudIcon(): string
    {
        return 'clipboard-list';
    }

    public function crudFields(): array
    {
        return [
            'service_category_id',
            'name',
            'description',
            'duration_minutes',
            'requires_approval',
            'is_active',
        ];
    }

    public function crudListFields(): array
    {
        return [
            'id',
            'service_category_id',
            'name',
            'duration_minutes',
            'requires_approval',
            'is_active',
        ];
    }

    public function crudLabels(): array
    {
        return [
            'id' => 'ID',
            'service_category_id' => 'Categoría',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'duration_minutes' => 'Duración (minutos)',
            'requires_approval' => 'Requiere aprobación',
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
            'service_category_id' => [
                'required',
                'exists:service_categories,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutional_services', 'name')
                    ->ignore($id)
                    ->where(fn ($query) => $query->where('service_category_id', request('service_category_id'))),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'duration_minutes' => [
                'nullable',
                'integer',
                'min:1',
                'max:1440',
            ],
            'requires_approval' => [
                'boolean',
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

    public function getRequiresApprovalSelect(): array
    {
        return [
            1 => 'Sí',
            0 => 'No',
        ];
    }
}
