<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix( 'admin' )->group(function(){
    Route::post('login', [ UserController::class, 'login' ])->name('admin.login');
    Route::get('alladmin', [ UserController::class, 'index' ])->name('admin.alladmin');
});
