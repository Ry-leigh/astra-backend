<?php

namespace App\Http\Controllers;

use App\Models\ClassCourse;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index($classCourseId) {
        $user = Auth::user();

        if ($user->hasRole(['Administrator', 'instructor'])) {
            $classCourse = ClassCourse::with([
                'course:id,name,code,description',
                'instructor.user:id,first_name,last_name,email',
            ])->findOrFail($classCourseId);

            $isInstructor = $user->roles->contains('name', 'Instructor');
            
            if ($isInstructor) {
                $instructor = $user->instructor;
                if (!$instructor || $instructor->id !== $classCourse->instructor_id) {
                    return response()->json(['message' => 'You can only view your own classes.'], 403);
                }
            }

            $today = Carbon::today();

            $past = Task::where('class_course_id', $classCourseId)
                ->whereDate('due_date', '<', $today)
                ->orderBy('due_date', 'desc')
                ->get();

            $todayTasks = Task::where('class_course_id', $classCourseId)
                ->whereDate('due_date', '=', $today)
                ->orderBy('due_time', 'asc')
                ->get();

            $upcoming = Task::where('class_course_id', $classCourseId)
                ->whereDate('due_date', '>', $today)
                ->orderBy('due_date', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'class' => $classCourse,
                'tasks' => [
                    'today' => $todayTasks,
                    'upcoming' => $upcoming,
                    'finished' => $past,
                ]
            ]);
        } else {
            $student = $user->student;
        }

        if ($student) {
            return $this->studentIndex($classCourseId);
        } else {
            return response()->json(['message' => 'Student profile not found.'], 403);
        }
    }

    public function studentIndex($classCourseId) {
        $user = Auth::user();
        $student = $user->student;

        $classCourse = ClassCourse::with([
                'course:id,name,code,description',
                'instructor.user:id,first_name,last_name,email',
            ])->findOrFail($classCourseId);

        $today = Carbon::today();

        $tasks = Task::with(['statuses' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->where('class_course_id', $classCourseId)->get();

        $finished = $tasks->filter(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished === true;
        })->values();

        $overdue = $tasks->filter(function ($t) use ($today) {
            return $t->due_date && Carbon::parse($t->due_date)->lt($today);
        })->reject(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

        $dueToday = $tasks->filter(function ($t) use ($today) {
            return $t->due_date && Carbon::parse($t->due_date)->isSameDay($today);
        })->reject(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

        $upcoming = $tasks->filter(function ($t) use ($today) {
            return $t->due_date && Carbon::parse($t->due_date)->gt($today);
        })->reject(function ($t) use ($student) {
            return $t->statuses->first()?->is_finished;
        })->values();

        return response()->json([
            'success' => true,
            'class' => $classCourse,
            'tasks' => [
                'overdue' => $overdue,
                'today' => $dueToday,
                'upcoming' => $upcoming,
                'finished' => $finished,
            ],
        ]);
    }

    public function store(Request $request, $classId) {
        $user = Auth::user();
        $classCourse = ClassCourse::findOrFail($classId);

        $isInstructor = $user->roles->contains('name', 'Instructor');
        if ($isInstructor) {
            $instructor = $user->instructor;
            if (!$instructor || $instructor->id !== $classCourse->instructor_id) {
                return response()->json(['message' => 'You can only create tasks in your own classes.'], 403);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable',
            'category' => 'required|in:assignment,project,quiz,exam,activity,other',
        ]);

        $task = Task::create([
            'class_course_id' => $classId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'] ?? null,
            'due_time' => $validated['due_time'] ?? null,
            'category' => $validated['category'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    public function update(Request $request, $classId, $id) {
        $user = Auth::user();
        $classCourse = ClassCourse::findOrFail($classId);
        $task = Task::where('class_course_id', $classId)->findOrFail($id);

        $isInstructor = $user->roles->contains('name', 'Instructor');
        if ($isInstructor) {
            $instructor = $user->instructor;
            if (!$instructor || $instructor->id !== $classCourse->instructor_id) {
                return response()->json(['message' => 'You can only update tasks in your own classes.'], 403);
            }
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'due_date' => 'sometimes|nullable|date',
            'due_time' => 'sometimes|nullable',
            'category' => 'sometimes|in:assignment,project,quiz,exam,activity,other',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    public function destroy($classId, $id) {
        $user = Auth::user();
        $classCourse = ClassCourse::findOrFail($classId);
        $task = Task::where('class_course_id', $classId)->findOrFail($id);

        $isInstructor = $user->roles->contains('name', 'Instructor');
        if ($isInstructor) {
            $instructor = $user->instructor;
            if (!$instructor || $instructor->id !== $classCourse->instructor_id) {
                return response()->json(['message' => 'You can only delete tasks in your own classes.'], 403);
            }
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }
}
