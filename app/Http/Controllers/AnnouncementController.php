<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Models\Program;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index() {
        // Admin - all announcements
        // Instructor - global, their role (Instructor), their program (if any), classrooms they handle
        // Officer (Class Officers) - global, their role (Officer), their program, their classrooms and courses they're taking
        // Student - global, their role (Student), their program, their classrooms and courses they're taking
        $user = Auth::user();

        // Take role names and respective role ids for this user
        $roleNames = $user->roles->pluck('name')->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();

        // Load announcement and its targets
        $query = Announcement::with(['targets'])->latest();

        // Query every announcement if user is Admininstrator
        if (in_array('Administrator', $roleNames)) {
            $announcements = $query->get();
        } 
        // Filter results if not Administrator
        else {
            $announcements = $query->whereHas('targets', function ($q) use ($user, $roleIds, $roleNames) {
                
                $q->where(function ($q2) use ($user, $roleIds, $roleNames) {

                    // Global announcements
                    $q2->orWhere(function ($q3) {
                        $q3->where('target_type', 'global')->whereNull('target_id');
                    });

                    // Role-based announcements
                    $q2->orWhere(function ($q3) use ($roleIds) {
                        $q3->where('target_type', 'role')->whereIn('target_id', $roleIds);
                    });

                    // Program-based announcements
                    if ($user->program_id) {
                        $q2->orWhere(function ($q3) use ($user) {
                            $q3->where('target_type', 'program')->where('target_id', $user->program_id);
                        });
                    }

                    // Classroom-based announcements
                    if ($user->classroom_id) {
                        $q2->orWhere(function ($q3) use ($user) {
                            $q3->where('target_type', 'classroom')->where('target_id', $user->classroom_id);
                        });
                    }

                    // Course-based announcements
                    // Instructor handled courses
                    if (in_array('Instructor', $roleNames) && $user->instructor && $user->instructor->classCourses) {
                        $courseIds = $user->instructor->classCourses->pluck('id')->toArray();
                        $q2->orWhere(function ($q3) use ($courseIds) {
                            $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                        });
                    }

                    // Student / Officer enrolled courses
                    if (array_intersect($roleNames, ['Student', 'Officer']) && $user->student && $user->student->enrollments) {
                        $courseIds = $user->student->enrollments->pluck('class_course_id')->toArray();
                        $q2->orWhere(function ($q3) use ($courseIds) {
                            $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                        });
                    }

                });
            })->get();
        }

        return response()->json($announcements);
    }

    public function show($id) {
        $announcement = Announcement::with(['targets'])->findOrFail($id);

        return response()->json($announcement);
    }

    public function create() {
        $user = Auth::user();

        $roleNames = $user->roles->pluck('name')->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();

        if (in_array('Administrator', $roleNames)) {

            $roles = Role::select('id', 'name')->get();

            $programs = Program::select('id', 'name')->get();

            $classrooms = Classroom::join('programs', 'classrooms.program_id', '=', 'programs.id')
                ->select('classrooms.id', DB::raw("CONCAT(programs.name, ' ', classrooms.year_level, classrooms.section) as name"))
                ->get();

            $classCourses = ClassCourse::with(['course', 'instructor.user', 'classroom.program'])
                ->get()
                ->map(fn($cc) => [
                    'id' => $cc->id,
                    'name' => sprintf(
                        '%s | %s - %s %s%s',
                        $cc->course->name,
                        match($cc->instructor->user->sex) {
                            'M' => 'Sir ',
                            'F' => "Ma'am ",
                            default => '',
                        } . $cc->instructor->user->first_name . ' ' . $cc->instructor->user->last_name,
                        $cc->classroom->program->name,
                        $cc->classroom->year_level,
                        $cc->classroom->section
                    )
                ]);
                
            return response()->json([
                'roles' => $roles,
                'programs' => $programs,
                'classrooms' => $classrooms,
                'class_courses' => $classCourses,
            ]);

        } elseif (in_array('Instructor', $roleNames)) {
            
            $instructor = $user->instructor;

            $classrooms = Classroom::join('programs', 'classrooms.program_id', '=', 'programs.id')
                ->whereHas('classCourses', fn($q) =>
                    $q->where('instructor_id', $instructor->id)
                )
                ->select('classrooms.id', DB::raw("CONCAT(programs.name, ' ', classrooms.year_level, classrooms.section) as name"))
                ->get();

            $classCourses = ClassCourse::with(['course', 'classroom.program'])
                ->where('instructor_id', $instructor->id)
                ->get()
                ->map(fn($cc) => [
                    'id' => $cc->id,
                    'name' => sprintf(
                        '%s | %s %s%s',
                        $cc->course->name,
                        $cc->classroom->program->name,
                        $cc->classroom->year_level,
                        $cc->classroom->section
                    )
            ]);

            return response()->json([
                'classrooms' => $classrooms,
                'class_courses' => $classCourses,
            ]);
            
        }
    }

    public function store(Request $request) {
        $user = Auth::user();

        // sample request structure
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
            'targets' => 'required|array',
            'targets.global' => 'boolean',
            'targets.roles' => 'array',
            'targets.programs' => 'array',
            'targets.classrooms' => 'array',
            'targets.class_courses' => 'array',
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'event_time' => $validated['event_time'] ?? null,
            'created_by' => $user->id,
        ]);

        $targets = [];

        // Global
        if (!empty($validated['targets']['global']) && $validated['targets']['global'] === true) {
            $targets[] = [
                'target_type' => 'global',
                'target_id' => null,
            ];
        } else {

            // referenced from the announcement_targets table
            $mapping = [
                'roles' => 'role',
                'programs' => 'program',
                'classrooms' => 'classroom',
                'class_courses' => 'course',
            ];

            foreach ($mapping as $key => $type) {
                if (!empty($validated['targets'][$key])) {
                    foreach ($validated['targets'][$key] as $id) {
                        $targets[] = [
                            'target_type' => $type,
                            'target_id' => $id,
                        ];
                    }
                }
            }
        }

        $announcement->targets()->createMany($targets);

        return response()->json(['message' => 'Announcement created successfully!']);
    }

    public function update(Request $request, $id) {
        $user = Auth::user();

        // sample request structure
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
            'targets' => 'required|array',
            'targets.global' => 'boolean',
            'targets.roles' => 'array',
            'targets.programs' => 'array',
            'targets.classrooms' => 'array',
            'targets.class_courses' => 'array',
        ]);

        $announcement = Announcement::findOrFail($id);

        $announcement->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'event_time' => $validated['event_time'] ?? null,
            'last_updated_by' => $user->id,
        ]);

        $targets = [];

        // Global
        if (!empty($validated['targets']['global']) && $validated['targets']['global'] === true) {
            $targets[] = [
                'target_type' => 'global',
                'target_id' => null,
            ];
        } else {

            // referenced from the announcement_targets table
            $mapping = [
                'roles' => 'role',
                'programs' => 'program',
                'classrooms' => 'classroom',
                'class_courses' => 'course',
            ];

            foreach ($mapping as $key => $type) {
                if (!empty($validated['targets'][$key])) {
                    foreach ($validated['targets'][$key] as $id) {
                        $targets[] = [
                            'target_type' => $type,
                            'target_id' => $id,
                        ];
                    }
                }
            }
        }

        $announcement->targets()->delete();
        $announcement->targets()->createMany($targets);

        return response()->json(['message' => 'Announcement updated successfully!']);
    }

    public function destroy($id) {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }

    // ---------- class-specific announcements CRUD methods ----------
    public function classIndex($classId) {
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

    public function classStore(Request $request, $classId) {
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

    public function classUpdate(Request $request, $classId, $id) {
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

    public function classDestroy($classId, $id) {
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
