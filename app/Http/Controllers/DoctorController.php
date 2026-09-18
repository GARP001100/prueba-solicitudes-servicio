<?php

namespace App\Http\Controllers;

use App\Models\DoctorProfile;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = DoctorProfile::query()
            ->with('availabilities')
            ->where('is_active', true)
            ->whereHas('availabilities', fn ($query) => $query->where('is_active', true))
            ->orderBy('full_name')
            ->get();

        return view('doctors.index', compact('doctors'));
    }

    public function show(DoctorProfile $doctor): View
    {
        abort_unless($doctor->is_active, 404);

        return view('doctors.show', compact('doctor'));
    }

    public function availability(Request $request, DoctorProfile $doctor, AppointmentService $appointmentService): JsonResponse
    {
        abort_unless($doctor->is_active, 404);

        $start = $request->query('start') ? Carbon::parse($request->query('start')) : now()->startOfDay();
        $end = $request->query('end') ? Carbon::parse($request->query('end')) : now()->addDays(7)->endOfDay();

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'full_name' => $doctor->full_name,
            ],
            'events' => $appointmentService->availabilityEvents($doctor, $start, $end),
        ]);
    }
}
