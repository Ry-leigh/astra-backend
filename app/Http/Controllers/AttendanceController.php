<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request, $sessionId = null)
    {
        $session = $sessionId
            ? ClassSession::with('classSchedule')->findOrFail($sessionId)
            : ClassSession::with('classSchedule')
                ->whereDate('session_date', '<=', now())
                ->orderByDesc('session_date')
                ->first();

        if (!$session) {
            return response()->json([
                'session' => null,
                'attendance_records' => [],
                'fallback_students' => Student::select('id', 'first_name', 'last_name')->get(),
                'message' => 'No class session found. Showing enrolled students as fallback.'
            ]);
        }

        $records = AttendanceRecord::with(['student', 'markedBy'])
            ->where('class_session_id', $session->id)
            ->get();

        return response()->json([
            'session' => $session,
            'attendance_records' => $records
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_session_id' => 'required|exists:class_sessions,id',
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:present,late,absent,excused,suspended',
            'time_in' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:255',
        ]);

        $session = ClassSession::findOrFail($validated['class_session_id']);
        $isIntegrityViolated = !Carbon::parse($session->session_date)->isSameDay(now());

        $record = AttendanceRecord::create([
            ...$validated,
            'marked_by' => Auth::id(),
            'integrity_flag' => $isIntegrityViolated,
        ]);

        return response()->json([
            'message' => 'Attendance record created successfully.',
            'record' => $record
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = AttendanceRecord::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:present,late,absent,excused,suspended',
            'time_in' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:255',
        ]);

        $record->update([
            ...$validated,
            'marked_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Attendance record updated successfully.',
            'record' => $record
        ]);
    }

    public function destroy($id)
    {
        $record = AttendanceRecord::findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Attendance record deleted successfully.']);
    }
}
