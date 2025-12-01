<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskStatusController extends Controller
{
    public function markFinished($classCourseId, $id) {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found.'], 403);
        }

        $status = TaskStatus::create([
            'task_id' => $id,
            'student_id' => $student->id,
            'is_finished' => true,
            'finished_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Task marked successfully.', 'data' => $status]);
    }

    public function markUnfinished($classCourseId, $id) {
        $user = Auth::user();
        $student = $user->student;

        $task = Task::findOrFail($id);

        TaskStatus::where('task_id', $id)
                ->where('student_id', $student->id)
                ->delete();

        return response()->json(['success' => true, 'message' => 'Task marked as unfinished.']);
    }
}
