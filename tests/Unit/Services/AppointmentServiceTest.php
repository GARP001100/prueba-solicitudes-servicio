<?php

namespace Tests\Unit\Services;

use App\Models\Appointment;
use App\Models\DoctorAvailability;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_duration_never_exceeds_sixty_minutes(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Consulta',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Dr. Demo',
            'professional_title' => 'Odontólogo',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        DoctorAvailability::query()->create([
            'doctor_profile_id' => $doctor->id,
            'day_of_week' => 1,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $startsAt = Carbon::parse('next Monday 08:00:00');
        $endAt = app(AppointmentService::class)->calculateEnd($service, $startsAt);

        $this->assertEquals(60, $startsAt->diffInMinutes($endAt));
    }

    public function test_approved_appointments_block_new_requests(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Valoración',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Dr. Approved',
            'professional_title' => 'Odontólogo',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        DoctorAvailability::query()->create([
            'doctor_profile_id' => $doctor->id,
            'day_of_week' => 2,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $start = Carbon::parse('next Tuesday 09:00:00');
        $end = $start->copy()->addMinutes(30);

        Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => Appointment::STATUS_APPROVED,
        ]);

        $this->assertTrue(app(AppointmentService::class)->hasApprovedOverlap($doctor, $start, $end));
    }
}
