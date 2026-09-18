<?php

namespace App\Http\Requests;

use App\Models\ServiceRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requester_name' => ['required', 'string', 'min:3', 'max:150'],
            'requester_email' => ['required', 'email', 'max:255'],
            'request_type' => ['required', Rule::in(array_keys(ServiceRequest::types()))],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'requester_name.required' => 'El nombre del solicitante es obligatorio.',
            'requester_name.min' => 'El nombre del solicitante debe tener al menos 3 caracteres.',
            'requester_name.max' => 'El nombre del solicitante no puede superar 150 caracteres.',
            'requester_email.required' => 'El correo electrónico es obligatorio.',
            'requester_email.email' => 'El correo electrónico no tiene un formato válido.',
            'requester_email.max' => 'El correo electrónico no puede superar 255 caracteres.',
            'request_type.required' => 'Debes seleccionar un tipo de solicitud.',
            'request_type.in' => 'Debes seleccionar un tipo de solicitud válido.',
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'description.max' => 'La descripción no puede superar 3000 caracteres.',
        ];
    }
}
