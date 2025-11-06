<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ClassCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassAnnouncementController extends Controller
{
    // ---------- class-specific announcements CRUD methods ----------
    public function index($classId) {
        $classCourse = ClassCourse::findOrFail($classId);

        $announcements = Announcement::whereHas('targets', function ($q) use ($classId) {
            $q->where('target_type', 'course')
              ->where('target_id', $classId);
        })->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'class_course_id' => $classCourse->id,
            'data' => $announcements,
        ]);
    }

    public function store(Request $request, $classId) {
        $classCourse = ClassCourse::findOrFail($classId);
        $user = Auth::user();

        // Permission: instructor can only post to their own class; admins can post anywhere
        $isInstructor = $user->roles->contains('name', 'Instructor');
        if ($isInstructor) {
            $instructor = $user->instructor;
            if (!$instructor || $classCourse->instructor_id !== $instructor->id) {
                return response()->json(['message' => 'You can only post announcements in your own classes.'], 403);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $announcement = Announcement::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_date' => $validated['event_date'] ?? null,
                'event_time' => $validated['event_time'] ?? null,
                'created_by' => $user->id,
            ]);

            // create a single announcement_target linking to this class_course
            $announcement->targets()->create([
                'target_type' => 'course',
                'target_id' => $classCourse->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully.',
                'data' => $announcement->fresh() // include targets if you want: ->load('targets')
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            // For debugging you can return $e->getMessage() but avoid in production
            return response()->json(['message' => 'Failed to create announcement.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $classId, $id) {
        $classCourse = ClassCourse::findOrFail($classId);
        $announcement = Announcement::whereHas('targets', function ($q) use ($classId) {
            $q->where('target_type', 'course')->where('target_id', $classId);
        })->findOrFail($id);

        $user = Auth::user();
        $isInstructor = $user->roles->contains('name', 'Instructor');
        if ($isInstructor) {
            $instructor = $user->instructor;
            if (!$instructor || $classCourse->instructor_id !== $instructor->id) {
                return response()->json(['message' => 'You can only update announcements in your own classes.'], 403);
            }
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'event_date' => 'sometimes|nullable|date',
            'event_time' => 'sometimes|nullable',
        ]);

        $announcement->update($validated + ['last_updated_by' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => $announcement,
        ]);
    }

    public function destroy($classId, $id) {
        $classCourse = ClassCourse::findOrFail($classId);
        $announcement = Announcement::whereHas('targets', function ($q) use ($classId) {
            $q->where('target_type', 'course')->where('target_id', $classId);
        })->findOrFail($id);

        $user = Auth::user();
        $isInstructor = $user->roles->contains('name', 'Instructor');
        if ($isInstructor) {
            $instructor = $user->instructor;
            if (!$instructor || $classCourse->instructor_id !== $instructor->id) {
                return response()->json(['message' => 'You can only delete announcements in your own classes.'], 403);
            }
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }
}
