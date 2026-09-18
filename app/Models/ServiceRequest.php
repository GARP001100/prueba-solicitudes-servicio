<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const TYPE_TECHNICAL_SUPPORT = 'technical_support';

    public const TYPE_INFORMATION = 'information';

    public const TYPE_ADMINISTRATIVE_PROCEDURE = 'administrative_procedure';

    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'requester_name',
        'requester_email',
        'request_type',
        'description',
        'status',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'Nueva',
            self::STATUS_IN_PROGRESS => 'En proceso',
            self::STATUS_COMPLETED => 'Finalizada',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_TECHNICAL_SUPPORT => 'Soporte técnico',
            self::TYPE_INFORMATION => 'Solicitud de información',
            self::TYPE_ADMINISTRATIVE_PROCEDURE => 'Trámite administrativo',
            self::TYPE_OTHER => 'Otra',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function typeLabel(): string
    {
        return self::types()[$this->request_type] ?? $this->request_type;
    }
}
