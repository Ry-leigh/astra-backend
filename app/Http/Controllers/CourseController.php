<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index() {
        $courses = Course::all();

        return response()->json(['courses' => $courses]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|unique:courses,name',
            'description' => 'nullable|string',
            'code'        => 'required|string|unique:courses,code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course = Course::create([
            'name'        => $request->name,
            'description' => $request->description,
            'code'        => $request->code,
        ]);

        return response()->json(['message' => 'Course created successfully.', 'course'  => $course], 201);
    }

    public function update(Request $request, $id) {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|unique:courses,name,' . $course->id,
            'description' => 'nullable|string',
            'code'        => 'sometimes|string|unique:courses,code,' . $course->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course->update($validator->validated());

        return response()->json(['message' => 'Course updated successfully.', 'course'  => $course]);
    }

    public function destroy($id) {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found.'], 404);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }
}
