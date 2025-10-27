<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    public function index($programId) {
        // Find the program, or fail with 404
        $program = Program::findOrFail($programId);

        // Retrieve all classrooms under that program
        $classrooms = Classroom::with('program')
            ->where('program_id', $program->id)
            ->get();

        return response()->json([
            'success' => true,
            'program' => $program->name,
            'data' => $classrooms,
        ]);
    }

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
