<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Request $request, $id)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $classCourse = ClassCourse::findOrFail($id);

        if ($user->hasRole('Instructor')) {
            if ($classCourse->instructor_id !== $user->instructor->id) {
                return response()->json(['message' => 'You can only enroll students in your own classes.'], 403);
            }
        }

        $exists = Enrollment::where([
            'class_course_id' => $id,
            'student_id' => $validated['student_id'],
        ])->exists();

        if ($exists) {
            return response()->json(['message' => 'Student already enrolled.'], 409);
        }

        $enrollment = Enrollment::create([
            'class_course_id' => $id,
            'student_id' => $validated['student_id'],
        ]);

        return response()->json(['message' => 'Student enrolled successfully.', 'data' => $enrollment], 201);
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found.'], 404);
        }

        $enrollment->delete();

        return response()->json(['success' => true, 'message' => 'Student unenrolled successfully.']);
    }
}
