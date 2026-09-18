<?php

namespace Database\Seeders;

use App\Models\ServiceRequest;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['requester_name' => 'Ana Torres', 'requester_email' => 'ana.torres@example.com', 'request_type' => ServiceRequest::TYPE_TECHNICAL_SUPPORT, 'description' => 'Solicito revisión del acceso al sistema institucional.', 'status' => ServiceRequest::STATUS_NEW],
            ['requester_name' => 'Carlos Méndez', 'requester_email' => 'carlos.mendez@example.com', 'request_type' => ServiceRequest::TYPE_INFORMATION, 'description' => 'Requiero información sobre el procedimiento de actualización de datos.', 'status' => ServiceRequest::STATUS_IN_PROGRESS],
            ['requester_name' => 'Laura Gómez', 'requester_email' => 'laura.gomez@example.com', 'request_type' => ServiceRequest::TYPE_ADMINISTRATIVE_PROCEDURE, 'description' => 'Solicito orientación para completar un trámite administrativo.', 'status' => ServiceRequest::STATUS_COMPLETED],
            ['requester_name' => 'Diego Ruiz', 'requester_email' => 'diego.ruiz@example.com', 'request_type' => ServiceRequest::TYPE_OTHER, 'description' => 'Solicito apoyo para identificar el canal adecuado de atención.', 'status' => ServiceRequest::STATUS_NEW],
            ['requester_name' => 'María López', 'requester_email' => 'maria.lopez@example.com', 'request_type' => ServiceRequest::TYPE_TECHNICAL_SUPPORT, 'description' => 'La aplicación presenta un error al consultar la información registrada.', 'status' => ServiceRequest::STATUS_IN_PROGRESS],
        ];

        foreach ($records as $record) {
            ServiceRequest::query()->updateOrCreate(
                ['requester_email' => $record['requester_email'], 'description' => $record['description']],
                $record,
            );
        }
    }
}
