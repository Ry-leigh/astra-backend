<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskStatusController extends Controller
{
    public function markFinished($classId, $taskId) {
        $student = Auth::user()->student;

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('class_course_id', $classId)
            ->exists();

        if (! $isEnrolled) {
            return response()->json(['error' => 'You are not enrolled in this class.'], 403);
        }

        $task = Task::where('id', $taskId)
            ->where('class_course_id', $classId)
            ->firstOrFail();

        $status = TaskStatus::updateOrCreate(
            ['task_id' => $task->id, 'student_id' => $student->id],
            ['is_finished' => true, 'finished_at' => Carbon::now()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Task marked as finished.',
            'data' => $status
        ]);
    }

    public function undoFinished($classId, $taskId)
    {
        $student = Auth::user()->student;

        // Check if student is enrolled in this class
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('class_course_id', $classId)
            ->exists();

        if (! $isEnrolled) {
            return response()->json(['error' => 'You are not enrolled in this class.'], 403);
        }

        $task = Task::where('id', $taskId)
            ->where('class_course_id', $classId)
            ->firstOrFail();

        $status = TaskStatus::where('task_id', $task->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $status) {
            return response()->json(['error' => 'You have not marked this task as finished yet.'], 404);
        }

        $status->update([
            'is_finished' => false,
            'finished_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task marked as unfinished.',
            'data' => $status
        ]);
    }
}
