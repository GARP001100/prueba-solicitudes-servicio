<?php

namespace Database\Factories;

use App\Models\DoctorAvailability;
use App\Models\DoctorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorAvailabilityFactory extends Factory
{
    protected $model = DoctorAvailability::class;

    public function definition(): array
    {
        return [
            'doctor_profile_id' => DoctorProfile::factory(),
            'day_of_week' => fake()->numberBetween(1, 5),
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ];
    }
}
