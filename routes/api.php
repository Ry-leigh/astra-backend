<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// api/admin
Route::middleware(['auth:sanctum', 'role:Administrator'])
     ->prefix('admin')
     ->group(function () {

    Route::get('/dashboard', function () {return response()->json(['message' => 'Welcome, Admin!']);});

});

// api/instructor
Route::middleware(['auth:sanctum', 'role:Instructor'])
     ->prefix('instructor')
     ->group(function () {

    Route::get('/dashboard', function () {return response()->json(['message' => 'Welcome, Mr./Ms.!']);});

});

// api/officer
Route::middleware(['auth:sanctum', 'role:Officer'])
     ->prefix('officer')
     ->group(function () {



});


// api/student
Route::middleware(['auth:sanctum', 'role:Student,Officer'])
     ->prefix('student')
     ->group(function () {

    Route::get('/dashboard', function () {return response()->json(['message' => 'Hello there!']);});

});