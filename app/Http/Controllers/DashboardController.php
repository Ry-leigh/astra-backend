<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
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
        if ($user->hasRole('Administrator')) {
            $schedules = ClassSchedule::with(['classCourse', 'classCourse.course:id,name,description,code,units', 'classCourse.instructor.user:id,sex,first_name,Last_name'])
            ->where('day_of_week', $todayDay)
            ->get();
        } elseif ($user->hasRole('Instructor')) {
            $schedules = ClassSchedule::with(['classCourse', 'classCourse.course:id,name,description,code,units', 'classCourse.instructor.user:id,sex,first_name,Last_name'])
                ->whereHas('classCourse', function ($query) use ($user) {
                    $query->where('instructor_id', $user->instructor->id);
                })
                ->where('day_of_week', $todayDay)
                ->get();
        } elseif (($user->hasRole('Officer') || $user->hasRole('Student')) && $user->student) {
            $classIds = $user->student->enrollments->pluck('class_course_id');
            $schedules = ClassSchedule::with(['classCourse', 'classCourse.course:id,name,description,code,units', 'classCourse.instructor.user:id,sex,first_name,Last_name'])
                ->whereIn('class_course_id', $classIds)
                ->where('day_of_week', $todayDay)
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => $greetings,
            'user' => $user,
            'date' => $now,
            'activeUsers' => $activeUsers,
            'studentCount' => $studentCount,
            'instructorCount' => $instructorCount,
            'schedule' => $schedules
        ]);
    }

    public function attendanceCount($year){
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

        // 2. Get actual attendance counts from DB
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

        $months = $months->map(function($month, $index) use ($attendance) {
            $record = $attendance->firstWhere('month_number', $index + 1);
            if ($record) {
                $month['present'] = (int) $record->present;
                $month['late'] = (int) $record->late;
                $month['absent'] = (int) $record->absent;
            }
            return $month;
        });

        $yearRange = DB::table('class_sessions')
            ->selectRaw('MIN(YEAR(session_date)) as min_year, MAX(YEAR(session_date)) as max_year')
            ->first();

        $minYear = $yearRange->min_year;
        $maxYear = $yearRange->max_year;

        $attendance = AttendanceRecord::where('status', 'late')->count();

        return response()->json([
            'success' => true,
            'attendance' => $months,
            'minYear' => $minYear,
            'maxYear' => $maxYear
        ]);
    }
}

