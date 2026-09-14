<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isStaff())) {
                abort(403, 'Unauthorized. Administrative privileges required.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()), 403, 'Unauthorized. Parent records are restricted to administrators and staff.');

        $query = Guardian::with(['user', 'students.user', 'students.schoolClass']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('occupation', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%");
        }

        $parents = $query->latest()->paginate(15)->withQueryString();

        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        $students = Student::with(['user', 'schoolClass'])->where('status', 'active')->get();
        return view('parents.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'password'          => ['required', 'string', 'min:8'],
            'occupation'        => ['nullable', 'string', 'max:255'],
            'relationship'      => ['required', 'string', 'max:50'],
            'national_id'       => ['nullable', 'string', 'max:50'],
            'emergency_contact' => ['nullable', 'string', 'max:50'],
            'student_ids'       => ['nullable', 'array'],
            'student_ids.*'     => ['exists:students,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $parentRole = Role::firstOrCreate(['slug' => 'parent'], [
                'name' => 'Parent', 'description' => 'Parent/Guardian access'
            ]);

            $user = User::create([
                'role_id'   => $parentRole->id,
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'] ?? null,
                'password'  => Hash::make($validated['password']),
                'is_active' => true,
                'status'    => 'active',
            ]);

            $guardian = Guardian::create([
                'user_id'           => $user->id,
                'occupation'        => $validated['occupation'] ?? null,
                'relationship'      => $validated['relationship'],
                'national_id'       => $validated['national_id'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
            ]);

            if (!empty($validated['student_ids'])) {
                $guardian->students()->sync($validated['student_ids']);
            }
        });

        return redirect()->route('parents.index')
            ->with('success', 'Parent/Guardian registered successfully.');
    }

    public function show(Guardian $parent)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isStaff()) {
            // Allowed roles for parent management.
        } elseif ($user->isParent() && $user->guardian?->id === $parent->id) {
            // Parents may view their own guardian profile.
        } else {
            abort(403, 'Unauthorized. You do not have access to this parent profile.');
        }

        $parent->load(['user', 'students.user', 'students.schoolClass', 'students.section']);
        return view('parents.show', compact('parent'));
    }

    public function edit(Guardian $parent)
    {
        $parent->load(['user', 'students']);
        $students = Student::with(['user', 'schoolClass'])->where('status', 'active')->get();
        $selectedStudentIds = $parent->students->pluck('id')->toArray();
        return view('parents.edit', compact('parent', 'students', 'selectedStudentIds'));
    }

    public function update(Request $request, Guardian $parent)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email,' . $parent->user_id],
            'phone'             => ['nullable', 'string', 'max:20'],
            'password'          => ['nullable', 'string', 'min:8'],
            'occupation'        => ['nullable', 'string', 'max:255'],
            'relationship'      => ['required', 'string', 'max:50'],
            'national_id'       => ['nullable', 'string', 'max:50'],
            'emergency_contact' => ['nullable', 'string', 'max:50'],
            'student_ids'       => ['nullable', 'array'],
            'student_ids.*'     => ['exists:students,id'],
        ]);

        DB::transaction(function () use ($validated, $parent) {
            if (auth()->user()->isAdmin()) {
                $parent->user->update([
                    'name'  => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            }

            if (auth()->user()->isAdmin() && !empty($validated['password'])) {
                $parent->user->update(['password' => Hash::make($validated['password'])]);
            }

            $parent->update([
                'occupation'        => $validated['occupation'] ?? null,
                'relationship'      => $validated['relationship'],
                'national_id'       => $validated['national_id'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
            ]);

            $parent->students()->sync($validated['student_ids'] ?? []);
        });

        return redirect()->route('parents.index')
            ->with('success', 'Parent/Guardian details updated successfully.');
    }

    public function destroy(Guardian $parent)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Unauthorized. Administrative privileges required.');

        $user = $parent->user;
        $parent->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent/Guardian profile deleted successfully.');
    }
}
