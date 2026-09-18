<?php

namespace App\Http\Requests;

use App\Models\ServiceRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage service requests') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(array_keys(ServiceRequest::statuses())),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Debes seleccionar un estado.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
