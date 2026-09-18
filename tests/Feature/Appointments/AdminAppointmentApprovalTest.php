<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use App\Models\DoctorAvailability;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAppointmentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_conflicts_and_approve_pending(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Valoración',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Laura Martínez',
            'professional_title' => 'Odontóloga',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        DoctorAvailability::query()->create([
            'doctor_profile_id' => $doctor->id,
            'day_of_week' => 4,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $first = Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Thursday 08:30:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Thursday 09:00:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $second = Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Thursday 08:45:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Thursday 09:15:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin, 'web')->patch('/admin/appointments/'.$first->id.'/approve');

        $response->assertOk();
        $this->assertSame(Appointment::STATUS_APPROVED, $first->fresh()->status);
        $this->assertSame(Appointment::STATUS_REJECTED, $second->fresh()->status);
    }

    public function test_admin_rejects_only_same_doctor_conflicts(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Valoración',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctorOne = DoctorProfile::query()->create(['full_name' => 'Laura', 'professional_title' => 'Odontóloga', 'specialty' => 'General', 'is_active' => true]);
        $doctorTwo = DoctorProfile::query()->create(['full_name' => 'Mario', 'professional_title' => 'Dentista', 'specialty' => 'General', 'is_active' => true]);

        $first = Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctorOne->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Monday 09:00:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Monday 09:30:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $otherDoctor = Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctorTwo->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Monday 09:00:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Monday 09:30:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $this->actingAs($admin, 'web')->patch('/admin/appointments/'.$first->id.'/approve');

        $this->assertSame(Appointment::STATUS_APPROVED, $first->fresh()->status);
        $this->assertSame(Appointment::STATUS_PENDING, $otherDoctor->fresh()->status);
    }

    public function test_manual_rejection_requires_reason(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Reseña',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create(['full_name' => 'Carmen', 'professional_title' => 'Odontóloga', 'specialty' => 'General', 'is_active' => true]);
        $appointment = Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Tuesday 10:00:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Tuesday 10:30:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin, 'web')->patch('/admin/appointments/'.$appointment->id.'/reject', ['reason' => '']);

        $response->assertSessionHasErrors('reason');
    }
}
