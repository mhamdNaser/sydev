<?php

use App\Http\Controllers\Api\IconCssController;
use Illuminate\Support\Facades\Route;

Route::get('/icons.css', [IconCssController::class, 'generate']);
