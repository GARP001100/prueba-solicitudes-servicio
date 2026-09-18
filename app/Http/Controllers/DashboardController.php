<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return view('admin.dashboard');
        }

        return view('user.dashboard');
    }
}
