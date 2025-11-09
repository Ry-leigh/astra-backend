<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\{
    DashboardController,
    AnnouncementController,
    CalendarScheduleController,
    ClassScheduleController,
    EnrollmentController,
    ProgramController,
    ClassroomController,
    CourseController,
    ClassController,
    AttendanceController,
    AuthController,
    TaskController,
    ClassAnnouncementController,
    UserController,
    NotificationController,
    SettingsController,
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes here are protected by Sanctum and filtered through middleware.
| Frontend paths remain consistent across roles — role-based restrictions
| are handled by policies, middleware, and controller logic.
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', [AuthController::class, 'user']);
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    | The landing page after login for all users (Admin, Instructor, Student).
    */
    Route::get('/dashboard', [DashboardController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Announcements
    |--------------------------------------------------------------------------
    | All users can view announcements. Only Admin and Instructor can create,
    | update, or delete them depending on ownership and role.
    */
    Route::apiResource('announcements', AnnouncementController::class);

    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    | Unified calendar for holidays, events, meetings, exams, and make-up classes.
    */
    Route::apiResource('calendar', CalendarScheduleController::class);
    Route::get('/calendar/create', [CalendarScheduleController::class, 'create']);

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    | Each role sees their relevant schedules. Admins can query any.
    */
    Route::prefix('schedule')->group(function () {
        Route::get('/', [ClassScheduleController::class, 'index']);
        Route::get('/{id}', [ClassScheduleController::class, 'show'])->middleware('role:Administrator');
        Route::post('/', [ClassScheduleController::class, 'store'])->middleware('role:Administrator');
        Route::put('/{id}', [ClassScheduleController::class, 'update'])->middleware('role:Administrator');
        Route::delete('/{id}', [ClassScheduleController::class, 'destroy'])->middleware('role:Administrator');
    });

    /*
    |--------------------------------------------------------------------------
    | Programs
    |--------------------------------------------------------------------------
    | Only Admin can manage programs (e.g., BSIS, BSOA).
    */
    Route::apiResource('programs', ProgramController::class)->middleware('role:Administrator');

    /*
    |--------------------------------------------------------------------------
    | Classrooms
    |--------------------------------------------------------------------------
    | Admin manages all classrooms; Instructors can view their assigned ones.
    */
    Route::prefix('classrooms')->group(function () {
        Route::get('/{programId}', [ClassroomController::class, 'index'])->middleware('role:Administrator');
        Route::post('/', [ClassroomController::class, 'store'])->middleware('role:Administrator');
        Route::put('/{id}', [ClassroomController::class, 'update'])->middleware('role:Administrator');
        Route::delete('/{id}', [ClassroomController::class, 'destroy'])->middleware('role:Administrator');
        Route::get('/query', [ClassroomController::class, 'query'])->middleware('role:Administrator');
    });

    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    | Admin manages courses; Instructors and Students view their own.
    */
    Route::prefix('courses')->group(function () {
        Route::get('/{classroomId}', [CourseController::class, 'index'])->middleware('role:Administrator');
        Route::post('/', [CourseController::class, 'store'])->middleware('role:Administrator');
        Route::put('/{id}', [CourseController::class, 'update'])->middleware('role:Administrator');
        Route::delete('/{id}', [CourseController::class, 'destroy'])->middleware('role:Administrator');
        Route::get('/query', [CourseController::class, 'query'])->middleware('role:Administrator');
    });

    /*
    |--------------------------------------------------------------------------
    | Class Hub (Core Module)
    |--------------------------------------------------------------------------
    | Shared between Admin, Instructor, and Students. Instructors manage
    | attendance, tasks, and class-specific announcements.
    */
    Route::get('/class', [ClassController::class, 'index']); // lists classes taught if instructor, list classes enrolled if student

    Route::prefix('class/{classCourseId}')->group(function () {
        // Index page methods
        Route::get('/', [ClassController::class, 'show']);
        Route::post('/{id}/enroll', [EnrollmentController::class, 'store'])->middleware('role:Administrator|Instructor');
        Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy'])->middleware('role:Administrator|Instructor');

        // Attendance page methods
        Route::get('/{student}/attendance', [AttendanceController::class, 'studentIndex']); //not yet created        
        Route::prefix('attendance')->group(function () {
            Route::get('/{sessionId?}', [AttendanceController::class, 'index']);
            Route::post('/', [AttendanceController::class, 'store'])->middleware('role:admin|instructor');
            Route::put('/{id}', [AttendanceController::class, 'update'])->middleware('role:admin|instructor');
            Route::delete('/{id}', [AttendanceController::class, 'destroy'])->middleware('role:admin|instructor');
        });


        // tasks page methods
        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store'])->middleware('role:Administrator|Instructor');
            Route::put('/{id}', [TaskController::class, 'update'])->middleware('role:Administrator|Instructor');
            Route::delete('/{id}', [TaskController::class, 'destroy'])->middleware('role:Administrator|Instructor');
        });

        // Class-specific announcements methods
        Route::prefix('announcements')->group(function () {
            Route::get('/', [ClassAnnouncementController::class, 'index']);
            Route::post('/', [ClassAnnouncementController::class, 'store']);
            Route::put('/{id}', [ClassAnnouncementController::class, 'update']);
            Route::delete('/{id}', [ClassAnnouncementController::class, 'destroy']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Users (Admin Only)
    |--------------------------------------------------------------------------
    | Centralized user management for system administrators.
    */
    Route::apiResource('users', UserController::class)->middleware('role:Administrator');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    | Every user can view, mark, and delete their own notifications.
    */
    Route::apiResource('notifications', NotificationController::class);

    /*
    |--------------------------------------------------------------------------
    | User Settings
    |--------------------------------------------------------------------------
    | Notification and personalization preferences.
    */
    Route::prefix('settings')->group(function () {
        Route::get('/preferences', [SettingsController::class, 'getPreferences']);
        Route::put('/preferences', [SettingsController::class, 'updatePreferences']);
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    | For self-service password updates and profile management.
    */
        Route::put('/password', [SettingsController::class, 'changePassword']);
    });
});
