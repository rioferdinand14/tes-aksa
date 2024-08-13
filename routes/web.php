<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/api/login', [AuthController::class, 'login']);
// Route::get('/api/divisions', [DivisionController::class, 'index']);
// Route::get('/api/employee', [EmployeeController::class, 'index']);
// Route::post('/api/employee', [EmployeeController::class, 'store']);
// Route::put('/api/employee/{id}', [EmployeeController::class, 'update']);