<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    public function index($programId)
    {
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
        $validator = Validator::make($request->all(), [
            'program_id'    => 'required|exists:programs,id',
            'year_level'    => 'required|string',
            'section'       => 'nullable|string',
            'academic_year' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $classroom = Classroom::create($request->only(['program_id', 'year_level', 'section', 'academic_year']));

        return response()->json(['success' => true, 'data' => $classroom], 201);
    }

    public function update(Request $request, Classroom $classroom) {
        $validator = Validator::make($request->all(), [
            'program_id'    => 'required|exists:programs,id',
            'year_level'    => 'required|string',
            'section'       => 'nullable|string',
            'academic_year' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $classroom->update($request->only(['program_id', 'year_level', 'section', 'academic_year']));

        return response()->json(['success' => true, 'data' => $classroom]);
    }

    public function destroy(Classroom $classroom) {
        $classroom->delete();

        return response()->json(['success' => true, 'message' => 'Classroom deleted successfully']);
    }
}
