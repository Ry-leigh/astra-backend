<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
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

        $classrooms = Classroom::with('program:id,name', 'academicYear:id,year_start,year_end')
            ->where('program_id', $program->id)
            ->orderBy('year_level')
            ->orderBy('section')
            ->get();

        $academicYear = AcademicYear::select('id', 'year_start', 'year_end')->get();

        return response()->json([
            'success' => true,
            'program' => $program,
            'classrooms' => $classrooms,
            'academic_years' =>$academicYear
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
            'year_level'    => 'required|integer',
            'section'       => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $validated['section'] = $validated['section'] ?: "";

        $classroom = Classroom::firstOrCreate([
            'program_id' => $validated['program_id'],
            'year_level' => $validated['year_level'],
            'section' => $validated['section'],
            'academic_year_id' => $validated['academic_year_id'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $classroom,
        ], 201);
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'program_id'    => 'required|exists:programs,id',
            'year_level'    => 'required|integer',
            'section'       => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Classroom updated successfully.',
            'data'    => $classroom
        ]);
    }

    public function destroy(Classroom $id) {
        $id->delete();

        return response()->json(['success' => true, 'message' => 'Classroom deleted successfully']);
    }
}
