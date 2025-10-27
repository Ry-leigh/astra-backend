<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'class_course_id' => 'required|exists:class_courses,id',
            'student_id'      => 'required|exists:students,id',
        ]);

        $user = Auth::user();

        $classCourse = ClassCourse::findOrFail($validated['class_course_id']);

        $roleNames = $user->roles->pluck('name')->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();

        if (in_array('Instructor', $roleNames)) {
            $instructor = $user->instructor;

            if (!$instructor) {
                return response()->json(['message' => 'Instructor profile not found.'], 403);
            }

            // Ensure instructor owns this class course
            if ($classCourse->instructor_id !== $instructor->id) {
                return response()->json([
                    'message' => 'You can only enroll students in your own classes.'
                ], 403);
            }
        }

        // Prevent duplicate enrollment
        $exists = Enrollment::where($validated)->exists();
        if ($exists) {
            return response()->json(['message' => 'Student already enrolled.'], 409);
        }

        $enrollment = Enrollment::create($validated);

        return response()->json(['message' => 'Student enrolled successfully.', 'data' => $enrollment], 201);
    }

    public function destroy($id)
        {
            $enrollment = Enrollment::find($id);

            if (!$enrollment) {
                return response()->json(['message' => 'Enrollment not found.'], 404);
            }

            $enrollment->delete();

            return response()->json(['message' => 'Student unenrolled successfully.']);
        }
}
