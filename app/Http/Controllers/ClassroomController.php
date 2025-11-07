<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    // view all classrooms under a specific program
    public function index($programId)
    {
        $program = Program::findOrFail($programId);

        $classrooms = Classroom::with('program:id,name')
            ->where('program_id', $program->id)
            ->orderBy('year_level')
            ->orderBy('section')
            ->get(['id', 'program_id', 'year_level', 'section', 'academic_year']);

        return response()->json([
            'success' => true,
            'program' => $program,
            'data' => $classrooms,
        ]);
    }

    // Instructor: view all classrooms they’re teaching in
    // public function instructorIndex()
    // {
    //     $user = Auth::user();
    //     $instructor = $user->instructor;

    //     if (!$instructor) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Instructor profile not found.'
    //         ], 403);
    //     }

    //     // Fetch all class courses tied to this instructor
    //     $classCourses = ClassCourse::with([
    //         'course:id,name,code,description',
    //         'classroom:id,program_id,year_level,section,academic_year',
    //         'classroom.program:id,name',
    //     ])
    //     ->where('instructor_id', $instructor->id)
    //     ->get(['id', 'course_id', 'classroom_id', 'instructor_id', 'semester']);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $classCourses
    //     ]);
    // }

    public function store(Request $request) {
        $validated = $request->validate([
            'program_id'    => 'required|exists:programs,id',
            'year_level'    => 'integer|string',
            'section'       => 'nullable|string',
            'academic_year' => 'required|string',
        ]);

        $classroom = Classroom::create($validated);

        return response()->json([
            'success' => true,
            'data' => $classroom,
        ], 201);
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'program_id'    => 'required|exists:programs,id',
            'year_level'    => 'required|string',
            'section'       => 'nullable|string',
            'academic_year' => 'required|string',
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Classroom updated successfully.',
            'data'    => $classroom
        ]);
    }

    public function destroy(Classroom $classroom) {
        $classroom->delete();

        return response()->json(['success' => true, 'message' => 'Classroom deleted successfully']);
    }
}
