<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_profile_id',
        'day_of_week',
        'starts_at',
        'ends_at',
        'slot_minutes',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'slot_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function getDescription(): string
    {
        $label = match ((int) $this->day_of_week) {
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            default => 'Sábado',
        };

        return sprintf('%s %s - %s', $label, $this->starts_at, $this->ends_at);
    }

    public function isWithinTimeRange(string $startsAt, string $endsAt): bool
    {
        $start = Carbon::parse($startsAt);
        $end = Carbon::parse($endsAt);

        return $start->between(
            Carbon::parse($this->starts_at),
            Carbon::parse($this->ends_at)->subSecond(),
        ) && $end->lte(Carbon::parse($this->ends_at));
    }
}
