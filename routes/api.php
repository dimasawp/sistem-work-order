<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/jobs', [JobController::class, 'index']);
// Route::post('/jobs', [JobController::class, 'store']);
// Route::put('/jobs/{id}', [JobController::class, 'update']);
// Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
// Route::post('/sync-employees', [EmployeeController::class, 'sync'])->name('employees.sync');

Route::post('/jobs/store', [JobController::class, 'store'])->name('jobs.store');
Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
