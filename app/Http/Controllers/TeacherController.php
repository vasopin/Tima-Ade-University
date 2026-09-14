<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function __construct()
    {
        // Only admin may create, edit, update or delete teachers
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    public function index(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Unauthorized. Teacher management is restricted to administrators.');

        $query = Teacher::with(['user', 'classTeacherOf']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhere('employee_id', 'like', "%$search%")
                  ->orWhere('specialization', 'like', "%$search%");
        }

        $teachers = $query->latest()->paginate(15)->withQueryString();
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->get();
        return view('teachers.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:6',
            'phone'            => 'nullable|string|max:20',
            'employee_id'      => 'required|string|unique:teachers,employee_id',
            'qualification'    => 'nullable|string|max:255',
            'specialization'   => 'nullable|string|max:255',
            'joining_date'     => 'nullable|date',
            'address'          => 'nullable|string|max:500',
            'gender'           => 'nullable|in:male,female,other',
            'date_of_birth'    => 'nullable|date',
            'emergency_contact'=> 'nullable|string|max:20',
            'is_class_teacher' => 'boolean',
            'class_teacher_of' => 'nullable|exists:school_classes,id',
        ]);

        DB::transaction(function () use ($validated) {
            $teacherRole = Role::where('slug', 'teacher')->first();
            $user = User::create([
                'role_id'  => $teacherRole->id,
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone'    => $validated['phone'] ?? null,
            ]);

            Teacher::create([
                'user_id'           => $user->id,
                'employee_id'       => $validated['employee_id'],
                'qualification'     => $validated['qualification'] ?? null,
                'specialization'    => $validated['specialization'] ?? null,
                'joining_date'      => $validated['joining_date'] ?? null,
                'address'           => $validated['address'] ?? null,
                'gender'            => $validated['gender'] ?? null,
                'date_of_birth'     => $validated['date_of_birth'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'is_class_teacher'  => $validated['is_class_teacher'] ?? false,
                'class_teacher_of'  => $validated['class_teacher_of'] ?? null,
            ]);
        });

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher added successfully!');
    }

    public function show(Teacher $teacher)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            // Administrators may inspect teacher profiles.
        } elseif ($user->isTeacher() && $user->teacher?->id === $teacher->id) {
            // Teachers may view their own profile.
        } else {
            abort(403, 'Unauthorized. You do not have access to this teacher profile.');
        }

        $teacher->load(['user', 'classTeacherOf']);
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $classes = SchoolClass::where('is_active', true)->get();
        return view('teachers.edit', compact('teacher', 'classes'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $teacher->user_id,
            'phone'            => 'nullable|string|max:20',
            'employee_id'      => 'required|string|unique:teachers,employee_id,' . $teacher->id,
            'qualification'    => 'nullable|string|max:255',
            'specialization'   => 'nullable|string|max:255',
            'joining_date'     => 'nullable|date',
            'address'          => 'nullable|string|max:500',
            'gender'           => 'nullable|in:male,female,other',
            'date_of_birth'    => 'nullable|date',
            'emergency_contact'=> 'nullable|string|max:20',
            'is_class_teacher' => 'boolean',
            'class_teacher_of' => 'nullable|exists:school_classes,id',
        ]);

        DB::transaction(function () use ($validated, $teacher, $request) {
            $teacher->user->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6']);
                $teacher->user->update(['password' => Hash::make($request->password)]);
            }

            $teacher->update([
                'employee_id'       => $validated['employee_id'],
                'qualification'     => $validated['qualification'] ?? null,
                'specialization'    => $validated['specialization'] ?? null,
                'joining_date'      => $validated['joining_date'] ?? null,
                'address'           => $validated['address'] ?? null,
                'gender'            => $validated['gender'] ?? null,
                'date_of_birth'     => $validated['date_of_birth'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'is_class_teacher'  => $validated['is_class_teacher'] ?? false,
                'class_teacher_of'  => $validated['class_teacher_of'] ?? null,
            ]);
        });

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        // Only admin may delete teachers
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $teacher->user->delete();
        return redirect()->route('teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}
