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
use App\Http\Controllers\Api\IconDownloadCopyController;
use App\Http\Controllers\Api\PermissionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/convert-image', [ImageController::class, 'convert']);
Route::get('/download-image/{fileName}', [ImageController::class, 'download']);

Route::get('/locale/{lang}', [LocaleController::class, 'setlocale']);
Route::get('active-languages', [LanguageController::class, 'active'])->name('active-languages');

Route::post('allicons/WithoutPagination', [IconController::class, 'allWithoutPagination'])->name('WithoutPagination');
Route::get('icon-categories/WithoutPagination', [IconCategoriesController::class, 'allWithoutPagination'])->name('allWithoutPagination');

Route::controller(IconDownloadCopyController::class)->group(function () {
    Route::get('/download-icon/{fileName}', 'download');
    Route::get('/download-count/{fileName}', 'downloadCount');
    Route::get('/get-icon-svg/{fileName}', 'getIconCode');
    Route::get('/get-icon-jsx/{fileName}', 'getIconCodeJsx');
});

Route::post('userLogin', [AuthController::class, 'userLogin']);

Route::prefix('admin')->group(function () {

    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

    Route::controller(AuthController::class)->group(function () {
        Route::post('adminregister', 'register')->name('adminregister');
        Route::post('adminLogin', 'adminLogin')->name('adminLogin');
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::controller(IconController::class)->group(function () {
            Route::post('allicons', 'index');
            Route::post('icons', 'store');
            Route::put('icons/{id}', 'update');
            Route::delete('icons/{id}', 'destroy');
            Route::patch('icons/{id}/status', 'changeStatus');
        });

        Route::controller(IconCategoriesController::class)->group(function () {
            Route::post('icon-categories/all', 'index');
            Route::post('icon-categories', 'store');
            Route::put('icon-categories/{id}', 'update');
            Route::delete('icon-categories/{id}', 'destroy');
            Route::patch('icon-categories/{id}/status', 'changeStatus');
        });

        Route::controller(AdminRoleController::class)->group(function () {
            Route::post('/allroles', 'index');
            Route::post('/roles', 'store');
            Route::put('/roles/{id}', 'update');
            Route::delete('/roles/{id}',  'destroy');
        });

        Route::controller(PermissionsController::class)->group(function () {
            Route::post('all-permissions', 'index')->name('permissions');
            Route::get('all-permissions', 'allPermissions')->name('all-permissions');
            Route::post('permissions', 'store')->name('store-permission');
            Route::put('permissions/{id}', 'update')->name('update-permission');
            Route::post('update-role-permissions/{role}', 'updateRolePermissions')->name('update-role-permissions');
            Route::delete('permissions/{id}', 'destroy')->name('delete-permission');
        });

        Route::controller(UserController::class)->group(function () {
            Route::post('all-users', 'index')->name('users');
            Route::get('all-users', 'allPermissions')->name('all-users');
            Route::post('users', 'store')->name('store-user');
            Route::put('users/{id}', 'update')->name('update-user');
            Route::delete('users/{id}', 'destroy')->name('delete-user');
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
