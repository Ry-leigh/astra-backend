<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceRecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarScheduleController;
use App\Http\Controllers\ClassCourseController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\UserController;

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

            Route::prefix('tasks')->group(function () {
                Route::put('/{id}', [TaskController::class, 'update']);
                Route::delete('/{id}', [TaskController::class, 'destroy']);
                Route::get('/', [TaskController::class, 'index']);
                Route::post('/', [TaskController::class, 'store']);
            });
        });
    });

    Route::prefix('enrollments')->group(function () {
        Route::post('/', [EnrollmentController::class, 'store']);
        Route::delete('/{id}', [EnrollmentController::class, 'destroy']);
    });
    
    Route::prefix('calendar-schedules')->group(function () {
        Route::get('/create', [CalendarScheduleController::class, 'create']); // json for create schedules modal form
        Route::get('/{id}', [CalendarScheduleController::class, 'show']); // view details of schedules->id == {id}
        Route::put('/{id}', [CalendarScheduleController::class, 'update']); // edit details of schedules->id == {id}
        Route::delete('/{id}', [CalendarScheduleController::class, 'destroy']); // delete schedules->id == {id}
        Route::get('/', [CalendarScheduleController::class, 'index']); // list all schedules
        Route::post('/', [CalendarScheduleController::class, 'store']); // create schedules
    });

    Route::prefix('calendar-schedules')->group(function () {
        Route::get('/create', [ClassScheduleController::class, 'create']); // json for create class schedules modal form
        Route::get('/{id}', [ClassScheduleController::class, 'show']); // view details of class_schedules->id == {id}
        Route::put('/{id}', [ClassScheduleController::class, 'update']); // edit details of class_schedules->id == {id}
        Route::delete('/{id}', [ClassScheduleController::class, 'destroy']); // delete class_schedules->id == {id}
        Route::get('/', [ClassScheduleController::class, 'index']); // list all class schedules
        Route::post('/', [ClassScheduleController::class, 'store']); // create class schedules
    });

    Route::prefix('class-sessions')->group(function () {
        Route::get('/{classScheduleId}', [ClassSessionController::class, 'index']);
        Route::put('/{id}', [ClassSessionController::class, 'update']);
        Route::delete('/{id}', [ClassSessionController::class, 'destroy']);
        Route::post('/', [ClassSessionController::class, 'store']);
    });

    Route::prefix('attendance-records')->group(function () {
        Route::get('/{sessionId?}', [AttendanceRecordController::class, 'index']);
        Route::put('/{id}', [AttendanceRecordController::class, 'update']);
        Route::delete('/{id}', [AttendanceRecordController::class, 'destroy']);
        Route::post('/', [AttendanceRecordController::class, 'store']);
    });

    Route::apiResource('users', UserController::class);
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

            Route::prefix('tasks')->group(function () {
                Route::put('/{id}', [TaskController::class, 'update']);
                Route::delete('/{id}', [TaskController::class, 'destroy']);
                Route::get('/', [TaskController::class, 'index']);
                Route::post('/', [TaskController::class, 'store']);
            });
        });
    });

    Route::prefix('enrollments')->group(function () {
        Route::post('/', [EnrollmentController::class, 'store']);
        Route::delete('/{id}', [EnrollmentController::class, 'destroy']);
    });

    Route::prefix('calendar-schedules')->group(function () {
        Route::get('/create', [CalendarScheduleController::class, 'create']); // json for create schedules modal form
        Route::get('/', [AnnouncementController::class, 'index']); // list all announcements
    });

    Route::prefix('class-schedules')->group(function () {
        Route::get('/', [ClassScheduleController::class, 'index']); // show their respective schedules
        Route::get('/{id}', [ClassScheduleController::class, 'show']); // view details of a schedule
    });

    Route::prefix('class-sessions')->group(function () {
        Route::get('/{classScheduleId}', [ClassSessionController::class, 'index']);
        Route::put('/{id}', [ClassSessionController::class, 'update']);
        Route::post('/', [ClassSessionController::class, 'store']);
    });

    Route::prefix('attendance-records')->group(function () {
        Route::get('/{sessionId?}', [AttendanceRecordController::class, 'index']);
        Route::put('/{id}', [AttendanceRecordController::class, 'update']);
        Route::post('/', [AttendanceRecordController::class, 'store']);
    });
});

// api/officer
Route::middleware(['auth:sanctum', 'role:Officer'])
     ->prefix('officer')
     ->group(function () {

    Route::prefix('class-sessions')->group(function () {
        Route::get('/{classScheduleId}', [ClassSessionController::class, 'index']);
        Route::put('/{id}', [ClassSessionController::class, 'update']);
        Route::post('/', [ClassSessionController::class, 'store']);
    });

    Route::prefix('attendance-records')->group(function () {
        Route::get('/{sessionId?}', [AttendanceRecordController::class, 'index']);
        Route::put('/{id}', [AttendanceRecordController::class, 'update']);
        Route::post('/', [AttendanceRecordController::class, 'store']);
    });
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

    Route::prefix('class/{class}')->group(function () {
        Route::get('/', [ClassCourseController::class, 'index']);
        Route::get('/announcements', [AnnouncementController::class, 'classIndex']);

        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'studentIndex']);
            Route::put('/{task}/complete', [TaskStatusController::class, 'markFinished']);
            Route::put('/{task}/undo', [TaskStatusController::class, 'undoFinished']);
        });
    });

    Route::prefix('calendar-schedules')->group(function () {
        Route::get('/create', [CalendarScheduleController::class, 'create']); // json for create schedules modal form
        Route::get('/', [AnnouncementController::class, 'index']); // list all announcements
    });

    Route::prefix('class-schedules')->group(function () {
        Route::get('/', [ClassScheduleController::class, 'index']); // show their respective schedules
        Route::get('/{id}', [ClassScheduleController::class, 'show']); // view details of a schedule
    });

    Route::prefix('class-sessions')->group(function () {
        Route::get('/{classScheduleId}', [ClassSessionController::class, 'index']);
    });

    Route::prefix('attendance-records')->group(function () {
        Route::get('/{sessionId?}', [AttendanceRecordController::class, 'index']);
    });
});
