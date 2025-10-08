<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);

Route::prefix('admin')->group(function () {
    Route::post('adminregister', [AuthController::class, 'register']);
    Route::post('adminLogin', [AuthController::class, 'adminLogin']);
    Route::get('alladmin', [UserController::class, 'index'])->name('alladmin');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
