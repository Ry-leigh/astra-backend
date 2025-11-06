<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    // List classes depending on the user's role
    public function index()
    {
        $user = Auth::user();

        $query = ClassCourse::with([
            'course:id,name,code,description',
            'instructor.user:id,first_name,last_name,email',
        ]);

        switch (true) {
            case $user->hasRole('Administrator'):
                break; // Admin sees all

            case $user->hasRole('Instructor') && $user->instructor:
                $query->where('instructor_id', $user->instructor->id);
                break;

            case $user->hasRole('Student') && $user->student:
                $classIds = $user->student->enrollments()
                    ->with('classCourse:id')
                    ->get()
                    ->pluck('classCourse.id');
                $query->whereIn('id', $classIds);
                break;

            default:
                return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($query->get());
    }

    // Show details of a specific class
    public function show($id)
    {
        $user = Auth::user();

        $classCourse = ClassCourse::with([
            'course:id,name,code,description',
            'instructor.user:id,first_name,last_name,email',
            'enrollments.student.user:id,first_name,last_name,email',
        ])->findOrFail($id);

        // Role-based access validation
        if ($user->hasRole('Instructor')) {
            if ($classCourse->instructor_id !== $user->instructor->id) {
                return response()->json(['message' => 'Unauthorized: Not your class.'], 403);
            }
        }

        if ($user->hasRole(['Student', 'Officer'])) {
            $isEnrolled = $user->student
                ->enrollments()
                ->where('class_course_id', $id)
                ->exists();

            if (! $isEnrolled) {
                return response()->json(['message' => 'Unauthorized: Not enrolled.'], 403);
            }
        }

        return response()->json($classCourse);
    }
}
