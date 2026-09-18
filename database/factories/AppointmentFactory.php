<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startsAt = now()->addDays(fake()->numberBetween(1, 10))->setTime(9, 0, 0);

        return [
            'user_id' => User::factory(),
            'doctor_profile_id' => DoctorProfile::factory(),
            'institutional_service_id' => InstitutionalService::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes(30),
            'status' => Appointment::STATUS_PENDING,
            'requires_approval' => true,
            'user_notes' => fake()->sentence(),
            'admin_notes' => null,
        ];
    }
}
