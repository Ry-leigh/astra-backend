<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Semester;
use Database\Seeders\CourseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    // List classes depending on the user's role
    public function index()
    {
        $user = Auth::user();

        $query = ClassCourse::with([
            'course:id,name,code,description',
            'instructor.user:id,first_name,last_name,email',
            'classroom.program:id,name',
        ]);

        switch (true) {
            case $user->hasRole('Administrator'):
                break; // Admin sees all

            case $user->hasRole('Instructor') && $user->instructor:
                $query->where('instructor_id', $user->instructor->id);
                break;

            case $user->hasRole('Officer') && $user->student:
                $classIds = $user->student->enrollments()
                    ->with('classCourse:id')
                    ->get()
                    ->pluck('classCourse.id');
                $query->whereIn('id', $classIds);
                break;

            case $user->hasRole('Student') && $user->student:
                $classIds = $user->student->enrollments()
                    ->with('classCourse:id')
                    ->get()
                    ->pluck('classCourse.id');
                $query->whereIn('id', $classIds);
                break;
            default:
                return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json(['success' => true, 'classes' => $query->get()]);
    }

    // Show details of a specific class
    public function show($id)
    {
        $user = Auth::user();

        $classCourse = ClassCourse::with([
            'course:id,name,code,description',
            'instructor.user:id,sex,first_name,last_name,email',
            'enrollments.student.user:id,sex,first_name,last_name,email',
            'enrollments.student.user.roles:id,name'
        ])->findOrFail($id);

        // Role-based access validation
        if ($user->hasRole('Instructor')) {
            if ($classCourse->instructor_id !== $user->instructor->id) {
                return response()->json(['message' => 'Unauthorized: Not your class.'], 403);
            }
        }

        if ($user->hasRole('Student') || $user->hasRole('Officer')) {
            $isEnrolled = $user->student
                ->enrollments()
                ->where('class_course_id', $id)
                ->exists();

            if (! $isEnrolled) {
                return response()->json(['message' => 'Unauthorized: Not enrolled.'], 403);
            }
        }

        return response()->json(['success' => true, 'class' => $classCourse]);
    }


    public function create($classroomId) {
        $currentCourses = ClassCourse::where('classroom_id', $classroomId)->pluck('course_id');
        $courses = Course::whereNotIn('id', $currentCourses)->get();
        $instructors = Instructor::select('id', 'user_id')
            ->with(['user:id,sex,first_name,last_name'])
            ->get();

        $classroomAcademicYear = Classroom::where('id', $classroomId)
            ->value('academic_year_id');

        $semesters = Semester::where('academic_year_id', $classroomAcademicYear)->get();

        return response()->json(['success' => true, 'courses' => $courses, 'instructors' => $instructors, 'semesters' => $semesters]);
    }

    public function store($classroomId, Request $request) {
        $validated = $request->validate([
            'course_id'         => 'required|exists:courses,id',
            'instructor_id'     => 'nullable|exists:instructors,id',
            'academic_year_id'  => 'required|exists:academic_years,id',
            'semester'          => 'nullable|integer',
            'color'             => 'nullable',
        ]);


    $semesterRecord = Semester::where('academic_year_id', $validated['academic_year_id'])
        ->where('semester', $validated['semester'])
        ->first();

    if (! $semesterRecord) {
        return response()->json([
            'success' => false,
            'message' => 'Semester not found.',
        ]);
    }

    $semesterId = $semesterRecord->id;

        $course = ClassCourse::create([
            'classroom_id'  => $classroomId,
            'course_id'     => $validated['course_id'],
            'instructor_id' => $validated['instructor_id'] ?? null,
            'semester_id'      => $semesterId,
            'color'         => $validated['color'],
        ]);

        return response()->json(['success' => true, 'message' => 'Course added successfully.', 'data' => $course], 201);
    }
}
