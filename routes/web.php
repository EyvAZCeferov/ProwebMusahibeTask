<?php

use App\Http\Controllers\System\FallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::fallback([FallbackController::class, 'index']);
