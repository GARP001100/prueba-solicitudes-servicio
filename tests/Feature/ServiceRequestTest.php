<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('service-requests.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_list_and_open_creation_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('service-requests.index'))->assertOk();
        $this->actingAs($user)->get(route('service-requests.create'))->assertOk();
    }

    public function test_user_can_create_a_valid_request_and_status_is_forced_to_new(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('service-requests.store'), [
            'requester_name' => 'Guillermo Rodriguez',
            'requester_email' => 'guillermo@example.com',
            'request_type' => ServiceRequest::TYPE_TECHNICAL_SUPPORT,
            'description' => 'Solicito soporte para recuperar el acceso al sistema.',
            'status' => ServiceRequest::STATUS_COMPLETED,
        ]);

        $record = ServiceRequest::query()->firstOrFail();
        $response->assertRedirect(route('service-requests.show', $record));
        $response->assertSessionHas('success');
        $this->assertSame(ServiceRequest::STATUS_NEW, $record->status);
    }

    public function test_creation_fields_are_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('service-requests.create'))
            ->post(route('service-requests.store'), [
                'requester_name' => 'Al',
                'requester_email' => 'correo-invalido',
                'request_type' => 'invalid',
                'description' => 'corta',
            ])
            ->assertRedirect(route('service-requests.create'))
            ->assertSessionHasErrors([
                'requester_name',
                'requester_email',
                'request_type',
                'description',
            ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('service-requests.store'), [])
            ->assertSessionHasErrors([
                'requester_name',
                'requester_email',
                'request_type',
                'description',
            ]);
    }

    public function test_user_can_see_detail_and_missing_id_returns_404(): void
    {
        $user = User::factory()->create();
        $record = ServiceRequest::factory()->newRequest()->create();

        $this->actingAs($user)->get(route('service-requests.show', $record))->assertOk();
        $this->actingAs($user)->get('/service-requests/999999')->assertNotFound();
    }

    public function test_administrator_can_change_status_to_in_progress_and_completed(): void
    {
        $admin = $this->administrator();
        $record = ServiceRequest::factory()->newRequest()->create();

        $this->actingAs($admin)
            ->patch(route('service-requests.update-status', $record), [
                'status' => ServiceRequest::STATUS_IN_PROGRESS,
            ])
            ->assertRedirect(route('service-requests.show', $record))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'id' => $record->id,
            'status' => ServiceRequest::STATUS_IN_PROGRESS,
        ]);

        $this->actingAs($admin)
            ->patch(route('service-requests.update-status', $record), [
                'status' => ServiceRequest::STATUS_COMPLETED,
            ])
            ->assertRedirect(route('service-requests.show', $record));

        $this->assertDatabaseHas('service_requests', [
            'id' => $record->id,
            'status' => ServiceRequest::STATUS_COMPLETED,
        ]);
    }

    public function test_regular_user_cannot_change_request_status(): void
    {
        $user = $this->regularUser();
        $record = ServiceRequest::factory()->newRequest()->create();

        $this->actingAs($user)
            ->patch(route('service-requests.update-status', $record), [
                'status' => ServiceRequest::STATUS_COMPLETED,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('service_requests', [
            'id' => $record->id,
            'status' => ServiceRequest::STATUS_NEW,
        ]);
    }

    public function test_only_administrator_sees_status_management_form(): void
    {
        $admin = $this->administrator();
        $user = $this->regularUser();
        $record = ServiceRequest::factory()->newRequest()->create();

        $this->actingAs($admin)
            ->get(route('service-requests.show', $record))
            ->assertOk()
            ->assertSee('Actualizar estado')
            ->assertDontSee('El estado de esta solicitud es administrado por el equipo responsable.');

        $this->actingAs($user)
            ->get(route('service-requests.show', $record))
            ->assertOk()
            ->assertDontSee('Actualizar estado')
            ->assertSee('El estado de esta solicitud es administrado por el equipo responsable.');
    }

    public function test_invalid_status_is_rejected_and_other_fields_are_not_modified(): void
    {
        $admin = $this->administrator();
        $record = ServiceRequest::factory()->newRequest()->create([
            'requester_name' => 'Nombre original',
            'requester_email' => 'original@example.com',
            'description' => 'Descripción original suficientemente extensa.',
        ]);

        $this->actingAs($admin)
            ->patch(route('service-requests.update-status', $record), [
                'status' => 'invalid',
                'requester_name' => 'Nombre modificado',
                'requester_email' => 'cambiado@example.com',
                'description' => 'Descripción alterada por el cliente.',
            ])
            ->assertSessionHasErrors('status');

        $record->refresh();
        $this->assertSame('Nombre original', $record->requester_name);
        $this->assertSame('original@example.com', $record->requester_email);
        $this->assertSame('Descripción original suficientemente extensa.', $record->description);
    }

    public function test_status_endpoint_only_changes_status(): void
    {
        $admin = $this->administrator();
        $record = ServiceRequest::factory()->newRequest()->create([
            'requester_name' => 'Nombre original',
            'requester_email' => 'original@example.com',
            'request_type' => ServiceRequest::TYPE_INFORMATION,
            'description' => 'Descripción original suficientemente extensa.',
        ]);

        $this->actingAs($admin)
            ->patch(route('service-requests.update-status', $record), [
                'status' => ServiceRequest::STATUS_IN_PROGRESS,
                'requester_name' => 'Ataque',
                'requester_email' => 'ataque@example.com',
                'request_type' => ServiceRequest::TYPE_OTHER,
                'description' => 'Intento de cambiar otros campos mediante mass assignment.',
            ])
            ->assertRedirect(route('service-requests.show', $record));

        $record->refresh();
        $this->assertSame(ServiceRequest::STATUS_IN_PROGRESS, $record->status);
        $this->assertSame('Nombre original', $record->requester_name);
        $this->assertSame('original@example.com', $record->requester_email);
        $this->assertSame(ServiceRequest::TYPE_INFORMATION, $record->request_type);
        $this->assertSame('Descripción original suficientemente extensa.', $record->description);
    }

    public function test_list_filters_by_status_and_type(): void
    {
        $user = User::factory()->create();
        ServiceRequest::factory()->create([
            'requester_name' => 'Visible estado',
            'status' => ServiceRequest::STATUS_NEW,
            'request_type' => ServiceRequest::TYPE_INFORMATION,
        ]);
        ServiceRequest::factory()->create([
            'requester_name' => 'Oculto estado',
            'status' => ServiceRequest::STATUS_COMPLETED,
            'request_type' => ServiceRequest::TYPE_OTHER,
        ]);

        $this->actingAs($user)
            ->get(route('service-requests.index', [
                'status' => ServiceRequest::STATUS_NEW,
            ]))
            ->assertSee('Visible estado')
            ->assertDontSee('Oculto estado');

        $this->actingAs($user)
            ->get(route('service-requests.index', [
                'request_type' => ServiceRequest::TYPE_OTHER,
            ]))
            ->assertSee('Oculto estado')
            ->assertDontSee('Visible estado');
    }

    public function test_list_searches_by_name_and_email(): void
    {
        $user = User::factory()->create();
        ServiceRequest::factory()->create([
            'requester_name' => 'Persona Buscable',
            'requester_email' => 'primero@example.com',
        ]);
        ServiceRequest::factory()->create([
            'requester_name' => 'Otra Persona',
            'requester_email' => 'correo.unico@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('service-requests.index', ['search' => 'Buscable']))
            ->assertSee('Persona Buscable')
            ->assertDontSee('Otra Persona');

        $this->actingAs($user)
            ->get(route('service-requests.index', ['search' => 'correo.unico']))
            ->assertSee('Otra Persona')
            ->assertDontSee('Persona Buscable');
    }

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function regularUser(): User
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('user');

        return $user;
    }
}
