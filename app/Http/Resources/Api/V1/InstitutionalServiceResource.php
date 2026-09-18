<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionalServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'duration_minutes' => (int) $this->duration_minutes,
            'requires_approval' => (bool) $this->requires_approval,
            'category' => [
                'id' => $this->serviceCategory?->id,
                'name' => $this->serviceCategory?->name,
            ],
        ];
    }
}
