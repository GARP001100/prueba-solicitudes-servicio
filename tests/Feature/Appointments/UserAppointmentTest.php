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

class UserAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_pending_or_approved_appointment(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('user');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Valoración inicial',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Laura Martínez',
            'professional_title' => 'Odontóloga',
            'specialty' => 'Valoración',
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

        $date = Carbon::parse('next Monday 08:00:00');

        $response = $this->actingAs($user, 'web')->postJson('/appointments', [
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => $date->toDateTimeString(),
            'user_notes' => 'Solicito una valoración inicial.',
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('appointment.id'));
    }

    public function test_user_cannot_send_foreign_user_id(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('user');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Limpieza',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Dr. Test',
            'professional_title' => 'Dentista',
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

        $otherUser = User::factory()->create();

        $response = $this->actingAs($user, 'web')->postJson('/appointments', [
            'user_id' => $otherUser->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Tuesday 09:00:00')->toDateTimeString(),
            'user_notes' => 'Prueba',
        ]);

        $response->assertStatus(201);
        $this->assertSame($user->id, Appointment::query()->first()->user_id);
    }

    public function test_user_can_cancel_pending_appointment(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('user');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Consulta',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Carla López',
            'professional_title' => 'Odontóloga',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        DoctorAvailability::query()->create([
            'doctor_profile_id' => $doctor->id,
            'day_of_week' => 3,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $user->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Wednesday 08:00:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Wednesday 09:00:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user, 'web')->patch('/appointments/'.$appointment->id.'/cancel');

        $response->assertStatus(200);
        $this->assertSame(Appointment::STATUS_CANCELLED, $appointment->fresh()->status);
    }

    public function test_overlapping_pending_requests_remain_pending(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('user');

        $category = ServiceCategory::query()->create(['name' => 'Odontología', 'is_active' => true]);
        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Consulta',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Pedro Ríos',
            'professional_title' => 'Dentista',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        DoctorAvailability::query()->create([
            'doctor_profile_id' => $doctor->id,
            'day_of_week' => 5,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $first = Appointment::query()->create([
            'user_id' => $user->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Friday 09:00:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Friday 10:00:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $second = Appointment::query()->create([
            'user_id' => User::factory()->create()->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => Carbon::parse('next Friday 09:30:00')->toDateTimeString(),
            'ends_at' => Carbon::parse('next Friday 10:30:00')->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $this->assertSame(Appointment::STATUS_PENDING, $first->fresh()->status);
        $this->assertSame(Appointment::STATUS_PENDING, $second->fresh()->status);
    }
}
