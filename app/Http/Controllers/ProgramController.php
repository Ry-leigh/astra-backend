<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index() {
        $programs = Program::all();
        $programs = Program::orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $programs]);
    }

    public function show($id) {
        $program = Program::findOrFail($id);

        return response()->json(['success' => true, 'data' => $program]);
    }

    public function store(Request $request) {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:programs,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $program = Program::create($request->only(['name', 'description']));

        return response()->json(['success' => true, 'data' => $program], 201);
    }

    public function update(Request $request, $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(['message' => 'Program not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:programs,name,' . $program->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $program->update($request->only(['name', 'description']));

        return response()->json(['success' => true, 'data' => $program]);
    }

    public function destroy($id) {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(['message' => 'Program not found.'], 404);
        }

        $program->delete();

        return response()->json(['success' => true, 'message' => 'Program deleted successfully']);
    }
}
    