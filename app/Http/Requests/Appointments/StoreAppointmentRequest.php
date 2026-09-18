<?php

namespace App\Http\Requests\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('create', Appointment::class);
    }

    public function rules(): array
    {
        return [
            'doctor_profile_id' => ['required', 'exists:doctor_profiles,id'],
            'institutional_service_id' => ['required', 'exists:institutional_services,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'user_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
