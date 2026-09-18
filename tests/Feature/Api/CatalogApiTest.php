<?php

namespace Tests\Feature\Api;

use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\DynamicCrudService;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_only_active_categories_are_returned(): void
    {
        ServiceCategory::query()->create([
            'name' => 'Visible Category',
            'description' => 'Visible',
            'is_active' => true,
        ]);

        ServiceCategory::query()->create([
            'name' => 'Hidden Category',
            'description' => 'Hidden',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/catalog/categories');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Visible Category')
            ->assertJsonMissing(['Hidden Category']);
    }

    public function test_only_active_services_are_returned(): void
    {
        $category = ServiceCategory::query()->create([
            'name' => 'Academic Services',
            'is_active' => true,
        ]);

        InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Active Service',
            'duration_minutes' => 30,
            'requires_approval' => true,
            'is_active' => true,
        ]);

        InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Inactive Service',
            'duration_minutes' => 45,
            'requires_approval' => false,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/catalog/services');

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Active Service'])
            ->assertJsonMissing(['Inactive Service']);
    }

    public function test_services_can_be_filtered_by_category(): void
    {
        $firstCategory = ServiceCategory::query()->create(['name' => 'Category A', 'is_active' => true]);
        $secondCategory = ServiceCategory::query()->create(['name' => 'Category B', 'is_active' => true]);

        InstitutionalService::query()->create([
            'service_category_id' => $firstCategory->id,
            'name' => 'Service in category A',
            'duration_minutes' => 20,
            'is_active' => true,
        ]);

        InstitutionalService::query()->create([
            'service_category_id' => $secondCategory->id,
            'name' => 'Service in category B',
            'duration_minutes' => 40,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/catalog/services?category='.$firstCategory->id);

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Service in category A'])
            ->assertJsonMissing(['Service in category B']);
    }

    public function test_services_can_be_filtered_by_search(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Search Category', 'is_active' => true]);

        InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Orientación de matrícula',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Solicitud de certificado',
            'duration_minutes' => 20,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/catalog/services?search=matr');

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => 'Orientación de matrícula'])
            ->assertJsonMissing(['Solicitud de certificado']);
    }

    public function test_per_page_has_a_maximum_limit(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Pagination Category', 'is_active' => true]);

        for ($index = 1; $index <= 60; $index++) {
            InstitutionalService::query()->create([
                'service_category_id' => $category->id,
                'name' => 'Service '.$index,
                'duration_minutes' => 30,
                'is_active' => true,
            ]);
        }

        $response = $this->getJson('/api/v1/catalog/services?per_page=500');

        $response->assertOk();
        $this->assertLessThanOrEqual(50, count($response->json('data')));
    }

    public function test_individual_service_includes_category_information(): void
    {
        $category = ServiceCategory::query()->create([
            'name' => 'Service Detail Category',
            'is_active' => true,
        ]);

        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Detailed service',
            'description' => 'A detailed description',
            'duration_minutes' => 45,
            'requires_approval' => true,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/catalog/services/'.$service->id);

        $response
            ->assertOk()
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.category.name', 'Service Detail Category')
            ->assertJsonPath('data.name', 'Detailed service');
    }

    public function test_inactive_service_returns_not_found(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Inactive Category', 'is_active' => true]);

        $service = InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Hidden service',
            'duration_minutes' => 15,
            'is_active' => false,
        ]);

        $this->getJson('/api/v1/catalog/services/'.$service->id)
            ->assertNotFound();
    }

    public function test_me_require_authentication(): void
    {
        $this->getJson('/api/v1/me')
            ->assertUnauthorized();
    }

    public function test_me_returns_roles_and_permissions_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.roles.0', 'admin');

        $this->assertNotEmpty($response->json('data.permissions'));
    }

    public function test_responses_do_not_expose_sensitive_fields(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Hidden Data Category', 'is_active' => true]);
        InstitutionalService::query()->create([
            'service_category_id' => $category->id,
            'name' => 'Visible service',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/v1/catalog/categories')
            ->assertJsonMissing(['password', 'remember_token']);

        $this->getJson('/api/v1/catalog/services')
            ->assertJsonMissing(['password', 'remember_token']);

        $this->getJson('/api/v1/me')
            ->assertJsonMissing(['password', 'remember_token']);
    }

    public function test_models_are_available_in_dynamic_crud(): void
    {
        $resources = app(DynamicCrudService::class)->availableResources();

        $this->assertTrue(collect($resources)->contains(fn (array $item) => $item['resource'] === 'service-categories'));
        $this->assertTrue(collect($resources)->contains(fn (array $item) => $item['resource'] === 'institutional-services'));
    }

    public function test_service_category_foreign_key_has_a_valid_select_metadata(): void
    {
        ServiceCategory::query()->create([
            'name' => 'Metadata category',
            'is_active' => true,
        ]);

        $meta = app(DynamicCrudService::class)->metadata('institutional-services');

        $field = collect($meta['fields'])->firstWhere('name', 'service_category_id');

        $this->assertNotNull($field);
        $this->assertSame('select', $field['type']);
        $this->assertNotEmpty($field['options']);
    }

    public function test_catalog_seeder_creates_expected_data(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->assertDatabaseHas('service_categories', ['name' => 'Asesoría académica']);
        $this->assertDatabaseHas('institutional_services', ['name' => 'Orientación de matrícula']);
    }
}
