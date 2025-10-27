<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use Illuminate\Http\Request;

class ClassCourseController extends Controller
{
    public function index($classroomId) {
        $classCourse = ClassCourse::with([
            'course:id,name,code,description',
            'instructor:id,user_id',
            'instructor.user:id,first_name,last_name,email',
            'enrollments' => function ($query) {$query->select('id', 'student_id', 'class_course_id');},
            'enrollments.student:id,user_id',
            'enrollments.student.user:id,first_name,last_name,email'
        ])->findOrFail($classroomId);

        return response()->json([
            'success' => true,
            'data' => $classCourse
        ]);
    }
}
