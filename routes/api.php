<?php

use App\Http\Controllers\Api\AdminRoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\IconController;
use App\Http\Controllers\Api\IconCategoriesController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\StateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/convert-image', [ImageController::class, 'convert']);
Route::get('/download-image/{fileName}', [ImageController::class, 'download']);

Route::get('/locale/{lang}', [LocaleController::class, 'setlocale']);
Route::get('active-languages', [LanguageController::class, 'active'])->name('active-languages');

Route::get('alladmin', [UserController::class, 'index'])->name('alladmin');


Route::get('all-countries', [CountryController::class, 'index'])->name('site-countries');
Route::get('all-states', [StateController::class, 'index'])->name('site-states');
Route::get('all-cities', [CityController::class, 'index'])->name('site-cities');

Route::post('userLogin', [AuthController::class, 'userLogin']);

Route::prefix('admin')->group(function () {

    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

    Route::controller(AuthController::class)->group(function () {
        Route::post('adminregister', 'register')->name('adminregister');
        Route::post('adminLogin', 'adminLogin')->name('adminLogin');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::controller(IconController::class)->group(function () {
            Route::get('icons', 'index');
            Route::post('icons', 'store');
            Route::put('icons/{id}', 'update');
            Route::delete('icons/{id}', 'destroy');
            Route::patch('icons/{id}/status', 'changeStatus');
        });

        Route::controller(IconCategoriesController::class)->group(function () {
            Route::get('icon-categories', 'index');
            Route::get('icon-categories/all', 'allWithoutPagination');
            Route::post('icon-categories', 'store');
            Route::put('icon-categories/{id}', 'update');
            Route::delete('icon-categories/{id}', 'destroy');
            Route::patch('icon-categories/{id}/status', 'changeStatus');
        });

        Route::controller(CountryController::class)->group(function () {
            Route::get('all-countries', 'index')->name('all-countries');
            Route::post('countries', 'allCountry')->name('countries');
            Route::post('store-country', 'store')->name('store-country');
            Route::post('update-country/{id}', 'update')->name('update-country');
            Route::get('delete-country/{id}', 'destroy')->name('delete-country');
            Route::post('delete-countries', 'destroyarray')->name('delete-countries');
        });

        Route::controller(StateController::class)->group(function () {
            Route::get('all-states', 'index')->name('all-states');
            Route::post('all-states-id/{id}', 'allstates')->name('all-states-id');
            Route::post('store-state', 'store')->name('store-state');
            Route::post('update-state/{id}', 'update')->name('update-state');
            Route::get('delete-state/{id}', 'destroy')->name('delete-state');
            Route::post('delete-states', 'destroyarray')->name('delete-states');
        });

        Route::controller(CityController::class)->group(function () {
            Route::get('all-cities', 'index')->name('all-cities');
            Route::post('all-cities-id/{id}', 'allcities')->name('all-cities-id');
            Route::post('store-city', 'store')->name('store-city');
            Route::post('update-city/{id}', 'update')->name('update-city');
            Route::get('delete-city/{id}', 'destroy')->name('delete-city');
            Route::post('delete-cities', 'destroyarray')->name('delete-cities');
        });

        Route::controller(AdminRoleController::class)->middleware('auth:sanctum')->group(function () {
            Route::get('/roles', 'index');       // get all roles (with pagination)
            Route::post('/roles', 'store');       // create role
            Route::get('/roles/{id}', 'show');    // show one role
            Route::put('/roles/{id}', 'update');  // update role
            Route::delete('/roles/{id}',  'destroy'); // delete role
            Route::patch('/roles/{id}/status', 'changeStatus'); // toggle status
        });



        Route::controller(LanguageController::class)->group(function () {
            Route::get('all-languages', 'index')->name('all-languages');
            Route::post('add-language', 'store')->name('add-language');
            Route::post('add-word/{slug}', 'addWordToAdminFile')->name('add-word');
            Route::post('show-translation/{slug}', 'show')->name('show-translation');
            Route::get('delete-language/{id}', 'destroy')->name('delete-language');
            Route::get('changestatus-language/{id}', 'changestatus')->name('changestatus-language');
        });
    });
});
