<?php

use App\Http\Controllers\AnnouncementController;
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

    // api/admin/announcements
    Route::prefix('announcements')->group(function () {
        Route::get('/create', [AnnouncementController::class, 'create']); // json for create announcement modal form
        Route::get('/{id}', [AnnouncementController::class, 'show']); // view details of announcement->id == {id}
        Route::put('/{id}', [AnnouncementController::class, 'update']); // edit details of announcement->id == {id}
        Route::delete('/{id}', [AnnouncementController::class, 'destroy']); // delete announcement->id == {id}
        Route::get('/', [AnnouncementController::class, 'index']); // list all announcements
        Route::post('/', [AnnouncementController::class, 'store']); // create announcement
    });

});

// api/instructor
Route::middleware(['auth:sanctum', 'role:Instructor'])
     ->prefix('instructor')
     ->group(function () {

    Route::get('/dashboard', function () {return response()->json(['message' => 'Welcome, Mr./Ms.!']);});

    // api/instructor/announcements
    Route::prefix('announcements')->group(function () {
        Route::get('/create', [AnnouncementController::class, 'create']); // json for create announcement modal form
        Route::get('/{id}', [AnnouncementController::class, 'show']); // view details of announcement->id == {id}
        Route::put('/{id}', [AnnouncementController::class, 'update']); // edit details of announcement->id == {id}
        Route::delete('/{id}', [AnnouncementController::class, 'destroy']); // delete announcement->id == {id}
        Route::get('/', [AnnouncementController::class, 'index']); // list all announcements
        Route::post('/', [AnnouncementController::class, 'store']); // create announcement
    });

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

    Route::prefix('announcements')->group(function () {
        Route::get('/{id}', [AnnouncementController::class, 'show']); // view details of announcement->id == {id}
        Route::get('/', [AnnouncementController::class, 'index']); // list all announcements
    });

});