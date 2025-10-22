<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:Administrator'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome, Admin!']);
    });
});

Route::middleware(['auth:sanctum', 'role:Faculty,Instructor'])->group(function () {

    Route::get('/instructor/dashboard', function () {
        return response()->json(['message' => 'Welcome, Mr./Ms.!']);
    });
});