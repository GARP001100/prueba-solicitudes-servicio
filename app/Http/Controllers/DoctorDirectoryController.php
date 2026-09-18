<?php

namespace App\Http\Controllers;

use App\Models\DoctorProfile;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class DoctorDirectoryController extends Controller
{
    public function index()
    {
        return app(DoctorController::class)->index();
    }

    public function show(DoctorProfile $doctor)
    {
        abort_unless($doctor->is_active, 404);

        return view('doctors.show', compact('doctor'));
    }

    public function availability(Request $request, DoctorProfile $doctor)
    {
        return app(DoctorController::class)->availability(
            $request,
            $doctor,
            app(AppointmentService::class),
        );
    }
}
