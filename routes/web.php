<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/', [JobController::class, 'viewCalendar']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->middleware('auth')->name('dashboard');

// halaman utama hanya bisa diakses setelah login
// Route::get('/', [JobController::class, 'viewCalendar'])->middleware('auth');

Route::get('/jobs/deliver', [JobController::class, 'deliver'])->name('jobs.deliver');
Route::get('/jobs/received', [JobController::class, 'received'])->name('jobs.received');
