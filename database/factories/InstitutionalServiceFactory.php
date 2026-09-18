<?php

namespace Database\Factories;

use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionalService>
 */
class InstitutionalServiceFactory extends Factory
{
    protected $model = InstitutionalService::class;

    public function definition(): array
    {
        return [
            'service_category_id' => ServiceCategory::factory(),
            'name' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph(),
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'requires_approval' => $this->faker->boolean(30),
            'is_active' => true,
        ];
    }
}
