<?php

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [JobController::class, 'viewCalendar']);
