<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\ClassCourse;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $now = now()->setTimezone('Asia/Manila');
        $hour = $now->format('H');

        if ($hour < 12) {
            $period = "morning";
        } elseif ($hour < 17) {
            $period = "afternoon";
        } elseif ($hour < 20) {
            $period = "evening";
        } else {
            $period = "day";
        }

        if ($user->hasRole('Instructor') || $user->hasRole('Administrator')) {
            $title = ($user->sex == 'M') ? "Sir " : (($user->sex == 'F') ? "Ma'am " : '');
            $greetings = "Good {$period} " . $title . $user->first_name;
        } else {
            $greetings = "Good {$period} " . $user->first_name;
        }

        $activeUsers = DB::table('personal_access_tokens')->count();
        $studentCount = DB::table('role_user')->whereIn('role_id', [3, 4])->count();
        $instructorCount = DB::table('role_user')->whereIn('role_id', [1, 2])->count();

        
        $todayDay = Carbon::now()->format('l');
        if ($user->hasRole('Instructor') || $user->instructor) {
            $schedules = ClassSchedule::with(['classCourse', 'classCourse.course:id,name,description,code,units', 'classCourse.instructor.user:id,sex,first_name,Last_name'])
                ->whereHas('classCourse', function ($query) use ($user) {
                    $query->where('instructor_id', $user->instructor->id);
                })
                ->where('day_of_week', $todayDay)
                ->get();
        }
        elseif ($user->hasRole('Administrator')) {
            $schedules = ClassSchedule::with(['classCourse', 'classCourse.course:id,name,description,code,units', 'classCourse.instructor.user:id,sex,first_name,Last_name'])
            ->where('day_of_week', -1)
            ->get();
        } elseif (($user->hasRole('Officer') || $user->hasRole('Student')) && $user->student) {
            $classIds = $user->student->enrollments->pluck('class_course_id');
            $schedules = ClassSchedule::with(['classCourse', 'classCourse.course:id,name,description,code,units', 'classCourse.instructor.user:id,sex,first_name,Last_name'])
                ->whereIn('class_course_id', $classIds)
                ->where('day_of_week', $todayDay)
                ->get();
        }

        if ($user->instructor) {
            $handledSubjects = ClassCourse::where('instructor_id', $user->instructor->id)->count();
            $handledStudents = Enrollment::whereIn('class_course_id', 
        ClassCourse::where('instructor_id', $user->instructor->id)->pluck('id')
    )->count();
$totalHours = ClassSchedule::whereIn(
        'class_course_id',
        ClassCourse::where('instructor_id', $user->instructor->id)->pluck('id')
    )
    ->get()
    ->sum(function ($schedule) {
        $start = Carbon::parse($schedule->start_time);
        $end   = Carbon::parse($schedule->end_time);

        // IMPORTANT: diff from start → end
        return $start->diffInHours($end);
    });
        }

        $latestAnnouncements = Announcement::latest()->take(3)->get();

        if ($user->student) {
            // $tasks = Task::whereIn('class_course_id', Enrollment::where('student_id', $user->student->id)->pluck('class_course_id'))->get();
        

        $student = $user->student;

        $today = Carbon::today();

        $tasks = Task::with(['statuses' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->get();

        $finished = $tasks->filter(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

        $overdue = $tasks->filter(function ($t) use ($today) {
            return $t->due_date && Carbon::parse($t->due_date)->lt($today);
        })->reject(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

        $dueToday = $tasks->filter(function ($t) use ($today) {
            return $t->due_date && Carbon::parse($t->due_date)->isSameDay($today);
        })->reject(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

        $upcoming = $tasks->filter(function ($t) use ($today) {
            return $t->due_date === null 
                || Carbon::parse($t->due_date)->gt($today);
        })->reject(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

    }

        return response()->json([
            'success' => true,
            'message' => $greetings,
            'user' => $user,
            'date' => $now,
            'activeUsers' => $activeUsers,
            'studentCount' => $studentCount,
            'instructorCount' => $instructorCount,
            'schedule' => $schedules ?? null,
            'handledSubjects' => $handledSubjects ?? null,
            'handledStudents' => $handledStudents ?? null,
            'totalHours' => $totalHours ?? null,
            'latestAnnouncements' => $latestAnnouncements,
            'tasks' => [
                'overdue' => $overdue ?? null,
                'today' => $dueToday ?? null,
                'upcoming' => $upcoming ?? null,
            ]
        ]);
    }

    public function attendanceCount($year){
        $user = Auth::user();
        if ($year == null) {
            $year = 2025;
        }

        $year = date('Y');

        $months = collect([
            'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'
        ])->map(fn($name, $index) => [
            'name' => $name,
            'present' => 0,
            'late' => 0,
            'absent' => 0,
        ]);

        if ($user->hasRole('Administrator')) {
        $attendance = AttendanceRecord::selectRaw('
            MONTH(class_sessions.session_date) AS month_number,
            SUM(CASE WHEN attendance_records.status = "present" THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN attendance_records.status = "late" THEN 1 ELSE 0 END) AS late,
            SUM(CASE WHEN attendance_records.status = "absent" THEN 1 ELSE 0 END) AS absent
        ')
        ->join('class_sessions', 'attendance_records.class_session_id', '=', 'class_sessions.id')
        ->whereYear('class_sessions.session_date', $year)
        ->groupBy('month_number')
        ->get();
        } elseif ($user->hasRole('Instructor')) {
        $attendance = AttendanceRecord::whereIn('class_session_id', ClassSession::whereIn('class_schedule_id', ClassSchedule::whereIn('class_course_id', ClassCourse::where('instructor_id', $user->instructor->id)->pluck('id'))->pluck('id'))->pluck('id'))->selectRaw('
            MONTH(class_sessions.session_date) AS month_number,
            SUM(CASE WHEN attendance_records.status = "present" THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN attendance_records.status = "late" THEN 1 ELSE 0 END) AS late,
            SUM(CASE WHEN attendance_records.status = "absent" THEN 1 ELSE 0 END) AS absent
        ')
        ->join('class_sessions', 'attendance_records.class_session_id', '=', 'class_sessions.id')
        ->whereYear('class_sessions.session_date', $year)
        ->groupBy('month_number')
        ->get();; 
        }        

        
        if ($attendance != null) {
            $months = $months->map(function($month, $index) use ($attendance) {
            $record = $attendance->firstWhere('month_number', $index + 1);
            if ($record) {
                $month['present'] = (int) $record->present;
                $month['late'] = (int) $record->late;
                $month['absent'] = (int) $record->absent;
            }
            return $month;
        });
        }

        $yearRange = DB::table('class_sessions')
            ->selectRaw('MIN(YEAR(session_date)) as min_year, MAX(YEAR(session_date)) as max_year')
            ->first();

        $minYear = $yearRange->min_year;
        $maxYear = $yearRange->max_year;

        return response()->json([
            'success' => true,
            'attendance' => $months,
            'minYear' => $minYear,
            'maxYear' => $maxYear
        ]);
    }
}

