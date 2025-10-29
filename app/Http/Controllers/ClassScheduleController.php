<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassScheduleController extends Controller
{
    public function index() {
        $user = Auth::user();

        if ($user->hasRole('Administrator')) {
            $schedules = ClassSchedule::with(['classCourse', 'instructor'])->get();
        }

        elseif ($user->hasRole('Instructor') && $user->instructor) {
            $schedules = ClassSchedule::with(['classCourse', 'instructor'])
                ->where('instructor_id', $user->instructor->id)
                ->get();
        }

        elseif ($user->hasRole('Student') && $user->student) {
            $classIds = $user->student->classCourses()->pluck('id');
            $schedules = ClassSchedule::with(['classCourse', 'instructor'])
                ->whereIn('class_course_id', $classIds)
                ->get();
        }

        else {
            return response()->json(['message' => 'Unauthorized or no schedules found.'], 403);
        }

        return response()->json($schedules);
    }

    public function show($id) {
        $schedule = ClassSchedule::with(['classCourse', 'instructor'])->findOrFail($id);
        return response()->json($schedule);
    }

    public function store(Request $request) {
        $user = Auth::user();

        if (!$user->hasRole('Administrator')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'class_course_id' => 'required|exists:class_courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string',
        ]);

        $schedule = ClassSchedule::create($validated);

        return response()->json([
            'message' => 'Class schedule created successfully.',
            'data' => $schedule,
        ], 201);
    }

    public function update(Request $request, $id) {
        $user = Auth::user();

        if (!$user->hasRole('Administrator')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $schedule = ClassSchedule::findOrFail($id);

        $validated = $request->validate([
            'class_course_id' => 'sometimes|exists:class_courses,id',
            'instructor_id' => 'sometimes|exists:instructors,id',
            'day_of_week' => 'sometimes|string',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'room' => 'nullable|string',
        ]);

        $schedule->update($validated);

        return response()->json([
            'message' => 'Class schedule updated successfully.',
            'data' => $schedule,
        ]);
    }

    public function destroy($id) {
        $user = Auth::user();

        if (!$user->hasRole('Administrator')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $schedule = ClassSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Class schedule deleted successfully.']);
    }
}
