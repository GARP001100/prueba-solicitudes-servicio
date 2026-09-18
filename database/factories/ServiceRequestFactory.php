<?php

namespace Database\Factories;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'requester_name' => fake()->name(),
            'requester_email' => fake()->safeEmail(),
            'request_type' => fake()->randomElement(array_keys(ServiceRequest::types())),
            'description' => fake()->sentence(12),
            'status' => fake()->randomElement(array_keys(ServiceRequest::statuses())),
        ];
    }

    public function newRequest(): static
    {
        return $this->state(fn (): array => ['status' => ServiceRequest::STATUS_NEW]);
    }
}
