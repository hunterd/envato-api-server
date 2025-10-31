<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TemplateKitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes
Route::get('/template-kits', [TemplateKitController::class, 'index']);
Route::get('/template-kits/{id}', [TemplateKitController::class, 'show']);
Route::get('/extensions/search', [TemplateKitController::class, 'search']);

// Protected routes requiring authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/template-kits', [TemplateKitController::class, 'store']);
    Route::put('/template-kits/{id}', [TemplateKitController::class, 'update']);
    Route::delete('/template-kits/{id}', [TemplateKitController::class, 'destroy']);

    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
