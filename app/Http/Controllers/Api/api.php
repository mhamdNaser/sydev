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

     Route::controller(CountryController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-countries', 'index')->name('all-countries');
        Route::post('countries', 'allCountry')->name('countries');
        Route::post('store-country', 'store')->name('store-country');
        Route::post('update-country/{id}', 'update')->name('update-country');
        Route::get('delete-country/{id}', 'destroy')->name('delete-country');
        Route::post('delete-countries', 'destroyarray')->name('delete-countries');
    });

    Route::controller(StateController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-states', 'index')->name('all-states');
        Route::post('all-states-id/{id}', 'allstates')->name('all-states-id');
        Route::post('store-state', 'store')->name('store-state');
        Route::post('update-state/{id}', 'update')->name('update-state');
        Route::get('delete-state/{id}', 'destroy')->name('delete-state');
        Route::post('delete-states', 'destroyarray')->name('delete-states');
    });

    Route::controller(CityController::class)->middleware('auth:sanctum')->group(function () {
        Route::get('all-cities', 'index')->name('all-cities');
        Route::post('all-cities-id/{id}', 'allcities')->name('all-cities-id');
        Route::post('store-city', 'store')->name('store-city');
        Route::post('update-city/{id}', 'update')->name('update-city');
        Route::get('delete-city/{id}', 'destroy')->name('delete-city');
        Route::post('delete-cities', 'destroyarray')->name('delete-cities');
    });
});
