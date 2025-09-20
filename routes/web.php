<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [JobController::class, 'landing'])->name('jobs.landing');
Route::get('/search', [JobController::class, 'search'])->name('jobs.search');

Route::get('/login', function () {
    return redirect()->route('jobs.landing');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/jobs/deliver', [JobController::class, 'deliver'])->name('jobs.deliver');
    Route::get('/jobs/received', [JobController::class, 'received'])->name('jobs.received');
    Route::get('/jobs/history', [JobController::class, 'history'])->name('jobs.history');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile/update-nik', [UserController::class, 'updateNik'])->name('profile.update.nik');
    Route::put('/profile/update-password', [UserController::class, 'updatePassword'])->name('profile.update.password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/generate-ticket/{deptId}', [JobController::class, 'generateTicket']);


    Route::put('/jobs/{job}/confirm', [JobController::class, 'confirm'])->name('jobs.confirm');
    Route::put('/jobs/{job}/reject', [JobController::class, 'reject'])->name('jobs.reject');


    // Route::post('/jobs/store', [JobController::class, 'store'])->name('jobs.store');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::post('/jobs/{job}/update-time', [JobController::class, 'updateTime'])->name('jobs.updateTime');
    Route::post('/employees/sync', [EmployeeController::class, 'sync'])->name('employees.sync');

    Route::get('/employees/by-department/{department}', [EmployeeController::class, 'byDepartment'])
        ->name('employees.byDepartment');
});
