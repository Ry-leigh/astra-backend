<?php

use App\Http\Controllers\AnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassCourseController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProgramController;

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

    Route::prefix('programs')->group(function () {
        Route::put('/{id}', [ProgramController::class, 'update']);
        Route::delete('/{id}', [ProgramController::class, 'destroy']);
        Route::get('/', [ProgramController::class, 'index']);
        Route::post('/', [ProgramController::class, 'store']);
    });

    Route::prefix('classrooms')->group(function () {
        Route::put('/{id}', [ClassroomController::class, 'update']); // edit a classroom
        Route::delete('/{id}', [ClassroomController::class, 'destroy']); // remove a classroom
        Route::get('/{program}', [ClassroomController::class, 'adminIndex']); // list all classrooms in {program}
        Route::post('/', [ClassroomController::class, 'store']); // add a classroom
    });

    Route::prefix('courses')->group(function () {
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
    });

    Route::prefix('class')->group(function () {
        Route::prefix('{class}')->group(function () {
            Route::get('/', [ClassCourseController::class, 'index']);
        
            Route::prefix('announcements')->group(function () {
                Route::put('/{id}', [AnnouncementController::class, 'classUpdate']);
                Route::delete('/{id}', [AnnouncementController::class, 'classDestroy']);
                Route::get('/', [AnnouncementController::class, 'classIndex']);
                Route::post('/', [AnnouncementController::class, 'classStore']);
            });
        });
    });

    Route::prefix('enrollments')->group(function () {
        Route::post('/', [EnrollmentController::class, 'store']);
        Route::delete('/{id}', [EnrollmentController::class, 'destroy']);
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

    Route::prefix('classrooms')->group(function () {
        Route::get('/', [ClassroomController::class, 'instructorIndex']);
    });

    Route::prefix('class')->group(function () {
        Route::prefix('{class}')->group(function () {
            Route::get('/', [ClassCourseController::class, 'index']);
        
            Route::prefix('announcements')->group(function () {
                Route::put('/{id}', [AnnouncementController::class, 'classUpdate']);
                Route::delete('/{id}', [AnnouncementController::class, 'classDestroy']);
                Route::get('/', [AnnouncementController::class, 'classIndex']);
                Route::post('/', [AnnouncementController::class, 'classStore']);
            });
        });
    });

    Route::prefix('enrollments')->group(function () {
        Route::post('/', [EnrollmentController::class, 'store']);
        Route::delete('/{id}', [EnrollmentController::class, 'destroy']);
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

    Route::prefix('class')->group(function () {
        Route::get('/{class}', [ClassCourseController::class, 'index']);
        Route::get('{class}/announcements', [AnnouncementController::class, 'classIndex']);
    });

});