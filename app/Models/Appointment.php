<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'doctor_profile_id',
        'institutional_service_id',
        'starts_at',
        'ends_at',
        'status',
        'user_notes',
        'admin_notes',
        'reject_reason',
        'reviewed_by',
        'reviewed_at',
        'requires_approval',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'requires_approval' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function institutionalService(): BelongsTo
    {
        return $this->belongsTo(InstitutionalService::class);
    }

    public function getDescription(): string
    {
        return $this->starts_at?->format('d/m/Y H:i') ?? 'Cita';
    }
}
