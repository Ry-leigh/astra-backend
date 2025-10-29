<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSessionController extends Controller
{
    public function index($classScheduleId) {
        $user = Auth::user();
        $schedule = ClassSchedule::with('classCourse.instructor.user')->findOrFail($classScheduleId);

        if ($user->hasRole('Administrator')) {

        } elseif ($user->hasRole('Instructor')) {
            if (!$user->instructor || $schedule->instructor_id !== $user->instructor->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        } elseif ($user->hasRole('Student')) {
            $isEnrolled = $user->student
                ? $user->student->classCourses()->where('id', $schedule->class_course_id)->exists()
                : false;
            if (!$isEnrolled) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $sessions = $schedule->classSessions()
            ->with(['substitute:id,first_name,last_name', 'markedBy:id,first_name,last_name'])
            ->orderBy('session_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    public function store(Request $request, $classScheduleId) {
        $user = Auth::user();
        $schedule = ClassSchedule::findOrFail($classScheduleId);

        if (! $user->hasAnyRole(['Administrator', 'Instructor', 'Officer'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'session_date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after_or_equal:time_in',
            'remarks' => 'nullable|string',
        ]);

        $weekday = strtolower(now()->parse($validated['session_date'])->format('l'));
        $expected = strtolower($schedule->day_of_week);
        $integrityFlag = $weekday !== $expected;

        if (ClassSession::where('class_schedule_id', $schedule->id)
            ->whereDate('session_date', $validated['session_date'])
            ->exists()) {
            return response()->json(['message' => 'Session already exists.'], 409);
        }

        $session = ClassSession::create([
            'class_schedule_id' => $schedule->id,
            'session_date' => $validated['session_date'],
            'time_in' => $validated['time_in'] ?? null,
            'time_out' => $validated['time_out'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'marked_by' => $user->id,
            'integrity_flag' => $integrityFlag,
        ]);

        return response()->json([
            'message' => 'Class session created successfully.',
            'data' => $session
        ], 201);
    }

    public function show($id) {
        $session = ClassSession::with([
            'classSchedule.classCourse.course',
            'substitute:id,first_name,last_name',
            'markedBy:id,first_name,last_name'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    public function update(Request $request, $id) {
        $user = Auth::user();
        $session = ClassSession::findOrFail($id);
        $schedule = $session->classSchedule;

        if (! $user->hasAnyRole(['Administrator', 'Instructor', 'Officer'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'session_date' => 'sometimes|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after_or_equal:time_in',
            'remarks' => 'nullable|string',
        ]);

        $integrityFlag = $session->integrity_flag;
        if (isset($validated['session_date'])) {
            $weekday = strtolower(now()->parse($validated['session_date'])->format('l'));
            $expected = strtolower($schedule->day_of_week);
            $integrityFlag = $weekday !== $expected;
        }

        $session->update(array_merge($validated, [
            'marked_by' => $user->id,
            'integrity_flag' => $integrityFlag,
        ]));

        return response()->json([
            'message' => 'Class session updated successfully.',
            'data' => $session
        ]);
    }

    public function destroy($id) {
        $user = Auth::user();

        if (! $user->hasRole('Administrator')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $session = ClassSession::findOrFail($id);
        $session->delete();

        return response()->json(['message' => 'Class session deleted successfully.']);
    }
}
