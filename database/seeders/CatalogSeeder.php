<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalog = [
            'Asesoría académica' => [
                'description' => 'Orientación y acompañamiento académico para estudiantes y personal.',
                'services' => [
                    [
                        'name' => 'Orientación de matrícula',
                        'description' => 'Acompañamiento para la inscripción de asignaturas y plan de estudio.',
                        'duration_minutes' => 30,
                        'requires_approval' => true,
                    ],
                ],
            ],
            'Servicios administrativos' => [
                'description' => 'Trámites y procesos administrativos institucionales.',
                'services' => [
                    [
                        'name' => 'Solicitud de certificado',
                        'description' => 'Trámite de solicitud, revisión y entrega de certificados institucionales.',
                        'duration_minutes' => 20,
                        'requires_approval' => false,
                    ],
                ],
            ],
            'Soporte tecnológico' => [
                'description' => 'Apoyo para acceso, uso y solución de problemas tecnológicos.',
                'services' => [
                    [
                        'name' => 'Soporte de acceso a plataforma',
                        'description' => 'Asistencia para recuperación de credenciales y acceso a plataformas institucionales.',
                        'duration_minutes' => 30,
                        'requires_approval' => false,
                    ],
                ],
            ],
            'Bienestar y orientación' => [
                'description' => 'Atención integral para bienestar estudiantil y orientación institucional.',
                'services' => [
                    [
                        'name' => 'Orientación de bienestar',
                        'description' => 'Acompañamiento para orientación y gestión de bienestar institucional.',
                        'duration_minutes' => 45,
                        'requires_approval' => true,
                    ],
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $details) {
            $category = ServiceCategory::query()->firstOrCreate(
                ['name' => $categoryName],
                [
                    'description' => $details['description'],
                    'is_active' => true,
                ],
            );

            foreach ($details['services'] as $serviceData) {
                $category->institutionalServices()->firstOrCreate(
                    ['name' => $serviceData['name']],
                    [
                        'description' => $serviceData['description'],
                        'duration_minutes' => $serviceData['duration_minutes'],
                        'requires_approval' => $serviceData['requires_approval'],
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
