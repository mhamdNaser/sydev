<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::post('userLogin', [AuthController::class, 'userLogin']);

// Public routes
Route::prefix('admin')->group(function () {
    Route::post('adminregister', [AuthController::class, 'register']);
    Route::post('adminLogin', [AuthController::class, 'adminLogin']);
});

// Protected routes (Session-based)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('me', function (Request $request) {
        return response()->json([
            'user' => new UserResource($request->user())
        ]);
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
