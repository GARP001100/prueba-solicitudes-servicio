<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\RejectAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->boolean('calendar')) {
            return $this->events($request);
        }

        return view('admin.appointments.index', [
            'appointments' => Appointment::query()
                ->with(['user', 'doctorProfile', 'institutionalService'])
                ->orderBy('starts_at')
                ->get(),
            'pendingAppointments' => Appointment::query()
                ->where('status', Appointment::STATUS_PENDING)
                ->with(['user', 'doctorProfile', 'institutionalService'])
                ->orderBy('starts_at')
                ->get(),
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $appointments = Appointment::query()
            ->with(['user', 'doctorProfile', 'institutionalService'])
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

    public function pending(): JsonResponse
    {
        $appointments = Appointment::query()
            ->where('status', Appointment::STATUS_PENDING)
            ->with(['user', 'doctorProfile', 'institutionalService'])
            ->orderBy('starts_at')
            ->get();

        return response()->json($appointments);
    }

    public function conflicts(): JsonResponse
    {
        $appointments = Appointment::query()
            ->where('status', Appointment::STATUS_PENDING)
            ->with(['user', 'doctorProfile', 'institutionalService'])
            ->get();

        $conflicts = $appointments->filter(function (Appointment $appointment) use ($appointments): bool {
            foreach ($appointments as $candidate) {
                if ($candidate->id === $appointment->id || $candidate->doctor_profile_id !== $appointment->doctor_profile_id) {
                    continue;
                }

                if ($candidate->starts_at < $appointment->ends_at && $candidate->ends_at > $appointment->starts_at) {
                    return true;
                }
            }

            return false;
        });

        return response()->json($conflicts->values()->all());
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return response()->json([
            'appointment' => $appointment->load(['user', 'doctorProfile', 'institutionalService']),
        ]);
    }

    public function approve(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('approve', $appointment);

        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return response()->json([
                'status' => false,
                'message' => 'Solo se pueden aprobar solicitudes pendientes.',
            ], 422);
        }

        $overlap = Appointment::query()
            ->where('doctor_profile_id', $appointment->doctor_profile_id)
            ->where('status', Appointment::STATUS_APPROVED)
            ->where('id', '!=', $appointment->id)
            ->where('starts_at', '<', $appointment->ends_at)
            ->where('ends_at', '>', $appointment->starts_at)
            ->exists();

        if ($overlap) {
            return response()->json([
                'status' => false,
                'message' => 'Ya existe otra cita aprobada que colisiona con esta solicitud.',
            ], 422);
        }

        $count = 0;

        try {
            DB::transaction(function () use ($appointment, &$count): void {
                $appointment->update([
                    'status' => Appointment::STATUS_APPROVED,
                    'admin_notes' => 'Aprobada por administración.',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

                $conflicts = Appointment::query()
                    ->where('doctor_profile_id', $appointment->doctor_profile_id)
                    ->where('id', '!=', $appointment->id)
                    ->where('status', Appointment::STATUS_PENDING)
                    ->where('starts_at', '<', $appointment->ends_at)
                    ->where('ends_at', '>', $appointment->starts_at)
                    ->get();

                foreach ($conflicts as $conflict) {
                    $conflict->update([
                        'status' => Appointment::STATUS_REJECTED,
                        'admin_notes' => 'Rechazada por conflicto de horario.',
                        'reject_reason' => 'La franja fue asignada a otra solicitud.',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                    $count++;
                }
            });
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => false,
                'message' => 'No fue posible aprobar la solicitud.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => "La solicitud fue aprobada. Se rechazaron {$count} conflictos.",
            'appointment' => $appointment->fresh(),
            'rejected_count' => $count,
        ]);
    }

    public function reject(Appointment $appointment, RejectAppointmentRequest $request): JsonResponse
    {
        $this->authorize('approve', $appointment);

        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return response()->json([
                'status' => false,
                'message' => 'Solo se pueden rechazar solicitudes pendientes.',
            ], 422);
        }

        $reason = $request->validated('reason');

        $appointment->update([
            'status' => Appointment::STATUS_REJECTED,
            'admin_notes' => $reason,
            'reject_reason' => $reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'La solicitud fue rechazada.',
            'appointment' => $appointment->fresh(),
        ]);
    }
}
