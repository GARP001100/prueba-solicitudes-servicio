<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view appointments') || $user->can('view all appointments') || $user->hasRole('admin');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->user_id || $user->can('view all appointments') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create appointments') || $user->hasRole('admin');
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->user_id && $user->can('cancel own appointments')
            || $user->can('approve appointments') || $user->hasRole('admin');
    }

    public function approve(User $user, ?Appointment $appointment = null): bool
    {
        return $user->can('approve appointments') || $user->hasRole('admin');
    }
}
