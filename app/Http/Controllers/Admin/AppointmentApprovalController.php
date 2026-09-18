<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\RejectAppointmentRequest;
use App\Models\Appointment;

class AppointmentApprovalController extends Controller
{
    public function index()
    {
        return view('admin.appointments.index', [
            'appointments' => Appointment::query()
                ->with(['user', 'doctorProfile', 'institutionalService'])
                ->orderBy('starts_at')
                ->get(),
        ]);
    }

    public function approve(Appointment $appointment)
    {
        return app(AdminAppointmentController::class)->approve(request(), $appointment);
    }

    public function reject(Appointment $appointment, RejectAppointmentRequest $request)
    {
        $this->authorize('approve', $appointment);

        $appointment->update([
            'status' => Appointment::STATUS_REJECTED,
            'admin_notes' => $request->validated('reason'),
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
