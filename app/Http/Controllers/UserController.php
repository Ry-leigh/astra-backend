<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index() {
        $users = User::with(['roles', 'instructor', 'student'])->get();
        return response()->json(['success' => true, 'users' => $users]);
    }

    public function create() {
        return response()->json([
            'success' => true,
            'roles' => Role::get(),
            'programs' => Program::get(),
            'classrooms' => Classroom::get()
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'nullable|in:M,F',
            'address' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'section' => 'nullable',
            'program_id' => 'nullable|exists:programs,id',
            'year_level' => 'nullable|integer|min:1|max:5',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $user->roles()->attach($validated['role_id']);

        $role = Role::find($validated['role_id'])->name;
        if ($role === 'Instructor') {
            Instructor::create([
                'user_id' => $user->id,
                'program_id' => $validated['program_id'] ?? null,
            ]);
        } elseif ($role === 'Student' || $role === 'Officer') {
            $section = $validated['section'];
            $classroom = Classroom::where('program_id', $validated['program_id'])
                        ->where('year_level', $validated['year_level'])
                        ->when($section !== null, function($query) use ($section) {
                            return $query->where('section', $section);
                        })
                        ->when($section === null, function($query) {
                            return $query->whereNull('section');
                        })
                        ->orderBy('section', 'asc')
                        ->first();
            Student::create([
                'user_id' => $user->id,
                'program_id' => $validated['program_id'] ?? null,
                'year_level' => $validated['year_level'] ?? 1,
                'classroom_id' => $classroom?->id
            ]);
        }

        return response()->json(['success' => true, 'message' => 'User created successfully', 'user' => $user]);
    }

    public function show($id) {
        $user = User::with(['roles', 'instructor.program', 'student.program'])->findOrFail($id);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'nullable|in:M,F',
            'address' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'section' => 'nullable',
            'program_id' => 'nullable|exists:programs,id',
            'year_level' => 'nullable|integer|min:1|max:5',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $user->roles()->sync([$validated['role_id']]);
        $role = Role::find($validated['role_id'])->name;

        if ($role === 'Instructor') {
            $user->student()?->delete();
            $user->instructor()->updateOrCreate(
                ['user_id' => $user->id],
                ['program_id' => $validated['program_id'] ?? null]
            );
        } elseif ($role === 'Student' || $role === 'Officer') {
            $section = $validated['section'];
            $classroom = Classroom::where('program_id', $validated['program_id'])
                        ->where('year_level', $validated['year_level'])
                        ->when($section !== null, function($query) use ($section) {
                            return $query->where('section', $section);
                        })
                        ->when($section === null, function($query) {
                            return $query->whereNull('section');
                        })
                        ->orderBy('section', 'asc')
                        ->first();
            
            $user->instructor()?->delete();
            $user->student()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'program_id' => $validated['program_id'] ?? null,
                    'year_level' => $validated['year_level'] ?? 1,
                    'classroom_id' => $classroom?->id
                ]
            );
        } else {
            $user->instructor()?->delete();
            $user->student()?->delete();
        }

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
