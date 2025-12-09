<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index($classroomId)
    {
        if ($classroomId == null) {
            $courses = ClassCourse::get();
            return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
        }

        $classroom = Classroom::with('program')->findOrFail($classroomId);

        $programName = $classroom->program ? $classroom->program->name : 'Unknown Program';
        $yearLevel = $classroom->year_level;
        $section = $classroom->section ? $classroom->section : '';
        $classroomName = trim("{$programName} {$yearLevel}{$section}");

        $courses = ClassCourse::with([
            'course:id,name,code,description',
            'instructor.user:id,sex,first_name,last_name,email'
        ])
        ->where('classroom_id', $classroom->id)
        ->orderBy('course_id')
        ->get();

        return response()->json([
            'success' => true,
            'classroom' => [
                'id' => $classroom->id,
                'name' => $classroomName,
                'program' => $programName,
                'academic_year_id' => $classroom->academic_year_id
            ],
            'courses' => $courses
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name'        => 'required|string|unique:courses,name',
            'description' => 'nullable|string',
            'code'        => 'nullable|string|unique:courses,code',
        ]);

        $course = Course::create($validated);

        return response()->json(['success' => true, 'message' => 'Course created successfully.', 'data'    => $course], 201);
    }

    public function update(Request $request, $id) {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|unique:courses,name,' . $course->id,
            'description' => 'nullable|string',
            'code'        => 'sometimes|string|unique:courses,code,' . $course->id,
        ]);

        $course->update($validated);

        return response()->json(['success' => true, 'message' => 'Course updated successfully.', 'data'    => $course]);
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
