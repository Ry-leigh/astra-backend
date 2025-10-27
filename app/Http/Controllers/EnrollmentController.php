<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'class_course_id' => 'required|exists:class_courses,id',
            'student_id'      => 'required|exists:students,id',
        ]);

        // Prevent duplicate enrollment
        $exists = Enrollment::where($validated)->exists();
        if ($exists) {
            return response()->json(['message' => 'Student already enrolled.'], 409);
        }

        $enrollment = Enrollment::create($validated);

        return response()->json([
            'message' => 'Student enrolled successfully.',
            'data' => $enrollment
        ], 201);
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
