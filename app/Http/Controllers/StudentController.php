<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct()
    {
        // Only admin may create, edit, update or delete students
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isStaff() && !auth()->user()->isRegistrar())) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        abort_unless($user && ($user->isAdmin() || $user->isStaff() || $user->isRegistrar()), 403, 'Unauthorized. Student management is restricted to administrators, staff, and registrars.');

        $query = Student::with(['user', 'schoolClass', 'section']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhere('student_id', 'like', "%$search%")
                    ->orWhere('roll_number', 'like', "%$search%")
                    ->orWhere('admission_number', 'like', "%$search%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $classes  = SchoolClass::where('is_active', true)->get();

        return view('students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes  = SchoolClass::where('is_active', true)->get();
        $sections = Section::all();
        return view('students.create', compact('classes', 'sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:6',
            'phone'            => 'nullable|string|max:20',
            'roll_number'      => 'required|string|unique:students,roll_number',
            'admission_number' => 'required|string|unique:students,admission_number',
            'school_class_id'  => 'required|exists:school_classes,id',
            'section_id'       => 'required|exists:sections,id',
            'admission_date'   => 'required|date',
            'gender'           => 'nullable|in:male,female,other',
            'date_of_birth'    => 'nullable|date',
            'address'          => 'nullable|string|max:500',
            'parent_name'      => 'nullable|string|max:255',
            'parent_phone'     => 'nullable|string|max:20',
            'parent_email'     => 'nullable|email',
            'blood_group'      => 'nullable|string|max:5',
            'status'           => 'required|in:active,inactive,graduated,expelled',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $studentRole = Role::where('slug', 'student')->first();
            $user = User::create([
                'role_id'  => $studentRole->id,
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone'    => $validated['phone'] ?? null,
            ]);

            Student::create([
                'user_id'          => $user->id,
                'roll_number'      => $validated['roll_number'],
                'admission_number' => $validated['admission_number'],
                'school_class_id'  => $validated['school_class_id'],
                'section_id'       => $validated['section_id'],
                'admission_date'   => $validated['admission_date'],
                'gender'           => $validated['gender'] ?? null,
                'date_of_birth'    => $validated['date_of_birth'] ?? null,
                'address'          => $validated['address'] ?? null,
                'parent_name'      => $validated['parent_name'] ?? null,
                'parent_phone'     => $validated['parent_phone'] ?? null,
                'parent_email'     => $validated['parent_email'] ?? null,
                'blood_group'      => $validated['blood_group'] ?? null,
                'status'           => $validated['status'],
            ]);
        });

        return redirect()->route('students.index')
            ->with('success', 'Student registered successfully!');
    }

    public function show(Student $student)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isStaff()) {
            // Allowed roles for student management.
        } elseif ($user->isStudent()) {
            if ($user->student?->id !== $student->id) {
                abort(403, 'Unauthorized. You can only view your own student profile.');
            }
        } elseif ($user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];
            if (!in_array($student->id, $allowedIds)) {
                abort(403, 'Unauthorized. This student is not linked to your parent account.');
            }
        } else {
            abort(403, 'Unauthorized.');
        }

        $student->load(['user', 'schoolClass', 'section', 'attendances']);
        if ($user->isAdmin() || $user->isStudent() || $user->isParent()) {
            $student->load('feePayments.feeStructure');
        }
        $attendanceSummary = [
            'present' => $student->attendances->where('status', 'present')->count(),
            'absent'  => $student->attendances->where('status', 'absent')->count(),
            'late'    => $student->attendances->where('status', 'late')->count(),
        ];
        $totalAttendance   = array_sum($attendanceSummary);
        $attendancePct     = $totalAttendance > 0 ? round(($attendanceSummary['present'] / $totalAttendance) * 100, 1) : 0;

        $canViewFinancials = $user->isAdmin() || $user->isStudent() || $user->isParent();
        $feesSummary = $canViewFinancials ? [
            'total_paid'    => $student->feePayments->where('status', 'paid')->sum('amount_paid'),
            'total_pending' => $student->feePayments->where('status', 'pending')->count(),
        ] : null;

        return view('students.show', compact('student', 'attendanceSummary', 'attendancePct', 'feesSummary', 'canViewFinancials'));
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $classes  = SchoolClass::where('is_active', true)->get();
        $sections = Section::where('school_class_id', $student->school_class_id)->get();
        return view('students.edit', compact('student', 'classes', 'sections'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $student->user_id,
            'phone'           => 'nullable|string|max:20',
            'roll_number'     => 'required|string|unique:students,roll_number,' . $student->id,
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id'      => 'required|exists:sections,id',
            'admission_date'  => 'required|date',
            'gender'          => 'nullable|in:male,female,other',
            'date_of_birth'   => 'nullable|date',
            'address'         => 'nullable|string|max:500',
            'parent_name'     => 'nullable|string|max:255',
            'parent_phone'    => 'nullable|string|max:20',
            'parent_email'    => 'nullable|email',
            'blood_group'     => 'nullable|string|max:5',
            'status'          => 'required|in:active,inactive,graduated,expelled',
        ]);

        DB::transaction(function () use ($validated, $student, $request) {
            $isStaff = auth()->user()->isStaff();
            if (!$isStaff) {
                $student->user->update([
                    'name'  => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                ]);
            }

            if (!$isStaff && $request->filled('password')) {
                $request->validate(['password' => 'min:6']);
                $student->user->update(['password' => Hash::make($request->password)]);
            }

            $student->update([
                'roll_number'     => $validated['roll_number'],
                'school_class_id' => $validated['school_class_id'],
                'section_id'      => $validated['section_id'],
                'admission_date'  => $validated['admission_date'],
                'gender'          => $validated['gender'] ?? null,
                'date_of_birth'   => $validated['date_of_birth'] ?? null,
                'address'         => $validated['address'] ?? null,
                'parent_name'     => $validated['parent_name'] ?? null,
                'parent_phone'    => $validated['parent_phone'] ?? null,
                'parent_email'    => $validated['parent_email'] ?? null,
                'blood_group'     => $validated['blood_group'] ?? null,
                'status'          => $isStaff ? $student->status : $validated['status'],
            ]);
        });

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        // Only admin may delete students
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $student->user->delete(); // Cascades to studentt
        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully!');
    }

    public function getSections(Request $request)
    {
        $sections = Section::where('school_class_id', $request->class_id)->get();
        return response()->json($sections);
    }
}
