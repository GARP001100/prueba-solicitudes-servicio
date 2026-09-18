<?php

namespace Database\Seeders;

use App\Models\DoctorAvailability;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class DentalSchedulingSeeder extends Seeder
{
    public function run(): void
    {
        $category = ServiceCategory::query()->firstOrCreate([
            'name' => 'Odontología general',
        ], [
            'description' => 'Servicios de agenda odontológica general.',
            'is_active' => true,
        ]);

        $services = [
            'Valoración integral' => 60,
            'Higiene oral y prevención' => 45,
            'Orientación odontológica' => 30,
        ];

        foreach ($services as $name => $duration) {
            InstitutionalService::query()->firstOrCreate([
                'service_category_id' => $category->id,
                'name' => $name,
            ], [
                'description' => 'Servicio activo para la agenda de profesionales.',
                'duration_minutes' => $duration,
                'requires_approval' => true,
                'is_active' => true,
            ]);
        }

        $doctorProfiles = [
            [
                'full_name' => 'Laura Martínez',
                'professional_title' => 'Odontóloga',
                'specialty' => 'Valoración integral',
                'bio' => 'Especialista en valoración integral y atención preventiva.',
                'phone' => 'Consultorio 201',
            ],
            [
                'full_name' => 'Daniel Rojas',
                'professional_title' => 'Odontólogo',
                'specialty' => 'Higiene oral y prevención',
                'bio' => 'Especialista en seguimiento de salud oral y prevención general.',
                'phone' => 'Consultorio 202',
            ],
            [
                'full_name' => 'Natalia Gómez',
                'professional_title' => 'Odontóloga',
                'specialty' => 'Orientación odontológica',
                'bio' => 'Atención preventiva y orientación para pacientes en consulta general.',
                'phone' => 'Consultorio 203',
            ],
        ];

        foreach ($doctorProfiles as $doctorData) {
            $doctor = DoctorProfile::query()->firstOrCreate([
                'full_name' => $doctorData['full_name'],
            ], [
                'user_id' => null,
                'professional_title' => $doctorData['professional_title'],
                'specialty' => $doctorData['specialty'],
                'bio' => $doctorData['bio'],
                'phone' => $doctorData['phone'],
                'is_active' => true,
            ]);

            foreach (range(1, 5) as $day) {
                DoctorAvailability::query()->firstOrCreate([
                    'doctor_profile_id' => $doctor->id,
                    'day_of_week' => $day,
                    'starts_at' => '08:00:00',
                    'ends_at' => '12:00:00',
                ], [
                    'slot_minutes' => 30,
                    'is_active' => true,
                ]);

                DoctorAvailability::query()->firstOrCreate([
                    'doctor_profile_id' => $doctor->id,
                    'day_of_week' => $day,
                    'starts_at' => '14:00:00',
                    'ends_at' => '17:00:00',
                ], [
                    'slot_minutes' => 30,
                    'is_active' => true,
                ]);
            }
        }
    }
}
