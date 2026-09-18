<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class AppointmentService
{
    public function calculateEnd(InstitutionalService $service, CarbonInterface $startsAt): CarbonInterface
    {
        return $startsAt->copy()->addMinutes((int) $service->duration_minutes);
    }

    public function hasApprovedOverlap(DoctorProfile $doctor, CarbonInterface $start, CarbonInterface $end): bool
    {
        return $doctor->appointments()
            ->where('status', Appointment::STATUS_APPROVED)
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->exists();
    }

    public function availabilityEvents(DoctorProfile $doctor, CarbonInterface $rangeStart, CarbonInterface $rangeEnd): array
    {
        $events = [];
        $dayCursor = $rangeStart->copy()->startOfDay();
        $finalDay = $rangeEnd->copy()->endOfDay();

        while ($dayCursor->lessThanOrEqualTo($finalDay)) {
            $dayOfWeek = (int) $dayCursor->dayOfWeek;
            $availability = $doctor->availabilities()
                ->where('is_active', true)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if ($availability) {
                $slotStart = Carbon::parse($dayCursor->toDateString().' '.$availability->starts_at);
                $slotEnd = Carbon::parse($dayCursor->toDateString().' '.$availability->ends_at);
                $step = $availability->slot_minutes > 0 ? $availability->slot_minutes : 30;

                while ($slotStart->copy()->addMinutes($step) <= $slotEnd) {
                    $candidateEnd = $slotStart->copy()->addMinutes($step);
                    if (! $this->hasApprovedOverlap($doctor, $slotStart, $candidateEnd)) {
                        $events[] = [
                            'title' => $doctor->full_name,
                            'start' => $slotStart->format('Y-m-d\TH:i:s'),
                            'end' => $candidateEnd->format('Y-m-d\TH:i:s'),
                            'status' => 'available',
                        ];
                    }

                    $slotStart->addMinutes($step);
                }
            }

            $dayCursor->addDay();
        }

        return $events;
    }
}
