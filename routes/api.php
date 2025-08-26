<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/events', [TaskController::class, 'index']);
Route::post('/events', [TaskController::class, 'store']);
Route::put('/events/{id}', [TaskController::class, 'update']);
Route::delete('/events/{id}', [TaskController::class, 'destroy']);
Route::post('/sync-employees', [EmployeeController::class, 'sync'])->name('employees.sync');
