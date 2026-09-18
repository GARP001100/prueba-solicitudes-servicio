<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorProfileFactory extends Factory
{
    protected $model = DoctorProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'professional_title' => fake()->randomElement([
                'Odontólogo general',
                'Especialista en ortodoncia',
                'Cirujano maxilofacial',
                'Endodoncista',
            ]),
            'specialty' => fake()->randomElement([
                'Consulta general',
                'Ortodoncia',
                'Implantología',
                'Endodoncia',
                'Estética dental',
            ]),
            'bio' => fake()->paragraph(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
