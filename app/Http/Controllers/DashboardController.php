<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
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
}
