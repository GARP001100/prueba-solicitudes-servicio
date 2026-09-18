<?php

namespace Tests\Feature\Doctors;

use App\Models\DoctorAvailability;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_active_doctors(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $doctor = DoctorProfile::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Laura Martínez',
            'professional_title' => 'Odontóloga',
            'specialty' => 'Valoración integral',
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

        $response = $this->actingAs($user, 'web')->get('/doctors');

        $response->assertOk();
        $response->assertSee('Laura Martínez');
    }

    public function test_inactive_doctors_are_not_shown(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $doctor = DoctorProfile::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Doctor oculto',
            'professional_title' => 'Especialista',
            'specialty' => 'General',
            'is_active' => false,
        ]);

        DoctorAvailability::query()->create([
            'doctor_profile_id' => $doctor->id,
            'day_of_week' => 1,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'web')->get('/doctors');

        $response->assertOk();
        $response->assertDontSee('Doctor oculto');
    }

    public function test_user_can_fetch_doctor_availability_endpoint(): void
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
            'full_name' => 'Daniel Rojas',
            'professional_title' => 'Odontólogo',
            'specialty' => 'Higiene',
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

        $response = $this->actingAs($user, 'web')->getJson('/doctors/'.$doctor->id.'/availability?start=2026-09-14T00:00:00&end=2026-09-21T00:00:00');

        $response->assertOk();
        $this->assertNotEmpty($response->json('events'));
    }

    public function test_profile_detail_view_contains_fullcalendar(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        $doctor = DoctorProfile::query()->create([
            'full_name' => 'Valeria Gómez',
            'professional_title' => 'Odontóloga',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'web')->get('/doctors/'.$doctor->id);

        $response->assertOk();
        $response->assertSee('data-doctor-calendar');
    }
}
