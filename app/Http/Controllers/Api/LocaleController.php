<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setlocale($lang)
    {
        $translations = trans('admin');
        return response()->json($translations);
    }
}
