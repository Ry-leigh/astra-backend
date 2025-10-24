<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index() {
        // Admin - all announcements
        // Instructor - global, their role (Instructor), their program (if any), classrooms they handle
        // Officer (Class Officers) - global, their role (Officer), their program, their classrooms and courses they're taking
        // Student - global, their role (Student), their program, their classrooms and courses they're taking
        $user = Auth::user();

        // Take role names and respective role ids for this user
        $roleNames = $user->roles->pluck('name')->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();

        // Load announcement and its targets
        $query = Announcement::with(['targets'])->latest();

        // Query every announcement if user is Admininstrator
        if (in_array('Administrator', $roleNames)) {
            $announcements = $query->get();
        } 
        // Filter results if not Administrator
        else {
            $announcements = $query->whereHas('targets', function ($q) use ($user, $roleIds, $roleNames) {
                
                $q->where(function ($q2) use ($user, $roleIds, $roleNames) {

                    // Global announcements
                    $q2->orWhere(function ($q3) {
                        $q3->where('target_type', 'global')->whereNull('target_id');
                    });

                    // Role-based announcements
                    $q2->orWhere(function ($q3) use ($roleIds) {
                        $q3->where('target_type', 'role')->whereIn('target_id', $roleIds);
                    });

                    // Program-based announcements
                    if ($user->program_id) {
                        $q2->orWhere(function ($q3) use ($user) {
                            $q3->where('target_type', 'program')->where('target_id', $user->program_id);
                        });
                    }

                    // Classroom-based announcements
                    if ($user->classroom_id) {
                        $q2->orWhere(function ($q3) use ($user) {
                            $q3->where('target_type', 'classroom')->where('target_id', $user->classroom_id);
                        });
                    }

                    // Course-based announcements
                    // Instructor handled courses
                    if (in_array('Instructor', $roleNames) && $user->instructor && $user->instructor->classCourses) {
                        $courseIds = $user->instructor->classCourses->pluck('id')->toArray();
                        $q2->orWhere(function ($q3) use ($courseIds) {
                            $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                        });
                    }

                    // Student / Officer enrolled courses
                    if (array_intersect($roleNames, ['Student', 'Officer']) && $user->student && $user->student->enrollments) {
                        $courseIds = $user->student->enrollments->pluck('class_course_id')->toArray();
                        $q2->orWhere(function ($q3) use ($courseIds) {
                            $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                        });
                    }

                });
            })->get();
        }

        return response()->json($announcements);
    }

    public function show(Announcement $announcement) {
        
    }

    public function create() {
        
    }

    public function store(Request $request) {

    }

    public function update(Request $request, Announcement $announcement) {

    }

    public function destroy() {

    }
}
