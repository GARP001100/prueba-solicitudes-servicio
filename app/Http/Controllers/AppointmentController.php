<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointments\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\InstitutionalService;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->boolean('calendar')) {
            return $this->events($request);
        }

        return view('appointments.index');
    }

    public function events(Request $request): JsonResponse
    {
        $appointments = Appointment::query()
            ->where('user_id', auth()->id())
            ->with(['doctorProfile', 'institutionalService'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->get()
            ->map(function (Appointment $appointment): array {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->doctorProfile?->full_name.' - '.($appointment->institutionalService?->name ?? 'Servicio'),
                    'start' => $appointment->starts_at?->format('Y-m-d\TH:i:s'),
                    'end' => $appointment->ends_at?->format('Y-m-d\TH:i:s'),
                    'status' => $appointment->status,
                    'backgroundColor' => match ($appointment->status) {
                        Appointment::STATUS_APPROVED => '#10b981',
                        Appointment::STATUS_PENDING => '#f59e0b',
                        Appointment::STATUS_REJECTED => '#ef4444',
                        default => '#64748b',
                    },
                ];
            });

        return response()->json($appointments);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return response()->json([
            'appointment' => $appointment->load(['doctorProfile', 'institutionalService', 'user']),
        ]);
    }

    public function store(StoreAppointmentRequest $request, AppointmentService $appointmentService): JsonResponse
    {
        $data = $request->validated();

        $doctor = DoctorProfile::query()->findOrFail($data['doctor_profile_id']);
        $service = InstitutionalService::query()->findOrFail($data['institutional_service_id']);

        abort_unless($doctor->is_active && $service->is_active, 404);

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = $appointmentService->calculateEnd($service, $startsAt);

        $availability = $doctor->availabilities()
            ->where('is_active', true)
            ->where('day_of_week', (int) $startsAt->dayOfWeek)
            ->first();

        abort_if(! $availability, 422, 'No existe disponibilidad para ese horario.');
        abort_if(! $availability->isWithinTimeRange($startsAt->format('H:i:s'), $endsAt->format('H:i:s')), 422, 'La cita está fuera del horario disponible.');
        abort_if($appointmentService->hasApprovedOverlap($doctor, $startsAt, $endsAt), 422, 'El doctor ya tiene una cita aprobada en ese rango.');

        $appointment = Appointment::query()->create([
            'user_id' => $request->user()->id,
            'doctor_profile_id' => $doctor->id,
            'institutional_service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $service->requires_approval ? Appointment::STATUS_PENDING : Appointment::STATUS_APPROVED,
            'requires_approval' => (bool) $service->requires_approval,
            'user_notes' => $data['user_notes'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'La cita fue agendada correctamente.',
            'appointment' => $appointment,
        ], 201);
    }

    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('cancel', $appointment);

        abort_if(! in_array($appointment->status, [Appointment::STATUS_PENDING, Appointment::STATUS_APPROVED], true), 422, 'La cita no puede cancelarse en su estado actual.');

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'admin_notes' => $appointment->admin_notes ?: 'Cancelada por el usuario.',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'La cita fue cancelada correctamente.',
            'appointment' => $appointment->fresh(),
        ]);
    }
}
