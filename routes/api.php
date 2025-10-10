<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LocaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('userLogin', [AuthController::class, 'userLogin']);

Route::get('/locale/{lang}', [LocaleController::class, 'setlocale']);
Route::get('active-languages', [LanguageController::class, 'active'])->name('active-languages');

Route::prefix('admin')->group(function () {
    Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });

    Route::post('adminregister', [AuthController::class, 'register']);
    Route::post('adminLogin', [AuthController::class, 'adminLogin']);
    Route::get('alladmin', [UserController::class, 'index'])->name('alladmin');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
