<?php

use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/catalog/categories', [CatalogController::class, 'categories']);
    Route::get('/catalog/categories/{serviceCategory}', [CatalogController::class, 'category']);
    Route::get('/catalog/services', [CatalogController::class, 'services']);
    Route::get('/catalog/services/{institutionalService}', [CatalogController::class, 'service']);
    Route::middleware('auth:sanctum')->get('/me', MeController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
