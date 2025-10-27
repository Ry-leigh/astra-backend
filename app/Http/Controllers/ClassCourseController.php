<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassCourseController extends Controller
{
    // shows a classroom's specified course descriptions(course name, instructor, code) and lists the enrollees in the class
    public function index($classId) {
        $user = Auth::user();

        $classCourse = ClassCourse::with([
            'course:id,name,code,description',
            'instructor:id,user_id',
            'instructor.user:id,first_name,last_name,email',
            'enrollments' => function ($query) {$query->select('id', 'student_id', 'class_course_id');},
            'enrollments.student:id,user_id',
            'enrollments.student.user:id,first_name,last_name,email'
        ])->findOrFail($classId);

        if (in_array('Instructor', $user->roles->pluck('name')->toArray())) {
            // Check if this instructor actually teaches this class
            if ($classCourse->instructor_id !== $user->instructor->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You are not assigned to this class.'
                ], 403);
            }
        }

        if (in_array('Student', $user->roles->pluck('name')->toArray()) || in_array('Officer', $user->roles->pluck('name')->toArray())) {
            $isEnrolled = $user->student
                ->enrollments()
                ->where('class_course_id', $classId)
                ->exists();
        
            if (! $isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You are not enrolled in this class.'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $classCourse
        ]);
    }
}
