<?php

use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\DynamicCrudController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorDirectoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/service-requests', [ServiceRequestController::class, 'index'])
        ->name('service-requests.index');

    Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])
        ->name('service-requests.create');

    Route::post('/service-requests', [ServiceRequestController::class, 'store'])
        ->name('service-requests.store');

    Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])
        ->whereNumber('serviceRequest')
        ->name('service-requests.show');

    Route::patch('/service-requests/{serviceRequest}/status', [ServiceRequestController::class, 'updateStatus'])
        ->whereNumber('serviceRequest')
        ->middleware('can:manage service requests')
        ->name('service-requests.update-status');

    Route::get('/doctors', [DoctorDirectoryController::class, 'index'])
        ->name('doctors.index');

    Route::get('/doctors/{doctor}/availability', [DoctorDirectoryController::class, 'availability'])
        ->whereNumber('doctor')
        ->name('doctors.availability');

    Route::get('/doctors/{doctor}', [DoctorDirectoryController::class, 'show'])
        ->whereNumber('doctor')
        ->name('doctors.show');

    Route::get('/appointments', [AppointmentController::class, 'index'])
        ->name('appointments.index');

    Route::get('/appointments/events', [AppointmentController::class, 'events'])
        ->name('appointments.events');

    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->name('appointments.store');

    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])
        ->whereNumber('appointment')
        ->name('appointments.show');

    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->whereNumber('appointment')
        ->name('appointments.cancel');

    Route::get('/ui-kit', function () {
        abort_unless(app()->isLocal(), 404);

        return view('admin.ui-kit');
    })->name('ui-kit');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])
        ->name('appointments.index');

    Route::get('/appointments/events', [AdminAppointmentController::class, 'events'])
        ->name('appointments.events');

    Route::get('/appointments/pending', [AdminAppointmentController::class, 'pending'])
        ->name('appointments.pending');

    Route::get('/appointments/conflicts', [AdminAppointmentController::class, 'conflicts'])
        ->name('appointments.conflicts');

    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])
        ->whereNumber('appointment')
        ->name('appointments.show');

    Route::patch('/appointments/{appointment}/approve', [AdminAppointmentController::class, 'approve'])
        ->whereNumber('appointment')
        ->name('appointments.approve');

    Route::patch('/appointments/{appointment}/reject', [AdminAppointmentController::class, 'reject'])
        ->whereNumber('appointment')
        ->name('appointments.reject');
});

Route::middleware(['auth', 'can:manage users'])
    ->prefix('admin/crud')
    ->name('admin.crud.')
    ->group(function () {
        Route::get('/', [
            DynamicCrudController::class,
            'home',
        ])->name('home');

        Route::get('/{resource}/data', [
            DynamicCrudController::class,
            'data',
        ])->name('data');

        Route::get('/{resource}/{id}', [
            DynamicCrudController::class,
            'show',
        ])->whereNumber('id')->name('show');

        Route::post('/{resource}', [
            DynamicCrudController::class,
            'store',
        ])->name('store');

        Route::patch('/{resource}/{id}', [
            DynamicCrudController::class,
            'update',
        ])->whereNumber('id')->name('update');

        Route::delete('/{resource}/{id}', [
            DynamicCrudController::class,
            'destroy',
        ])->whereNumber('id')->name('destroy');

        Route::get('/{resource}', [
            DynamicCrudController::class,
            'index',
        ])->name('index');
    });

require __DIR__.'/auth.php';
