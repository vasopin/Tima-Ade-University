<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized. Administrative privileges required.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::with(['role', 'student', 'teacher', 'guardian'])
            ->whereHas('role', fn ($role) => $role->where('slug', '!=', Role::STAFF));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        $stats = [
            'total'     => (clone $query)->toBase()->count(),
            'active'    => (clone $query)->where('status', 'active')->toBase()->count(),
            'pending'   => (clone $query)->where('status', 'pending')->toBase()->count(),
            'suspended' => (clone $query)->where('status', 'suspended')->toBase()->count(),
        ];

        return view('users.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::whereIn('slug', [
            Role::TEACHER,
            Role::STUDENT,
            Role::PARENT,
            Role::ADMISSIONS_OFFICER,
            Role::FINANCE_OFFICER,
            Role::REGISTRAR,
            Role::HR_OFFICER,
            Role::LIBRARIAN,
            Role::DEAN,
        ])->get();
        return view('users.create', ['roles' => $roles, 'faculties' => Faculty::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role_id'  => [
                'required',
                Rule::exists('roles', 'id')->whereIn('slug', [
                    Role::STAFF,
                    Role::TEACHER,
                    Role::STUDENT,
                    Role::PARENT,
                    Role::ADMISSIONS_OFFICER,
                    Role::FINANCE_OFFICER,
                    Role::REGISTRAR,
                    Role::HR_OFFICER,
                    Role::LIBRARIAN,
                ]),
            ],
            'password' => ['required', 'string', 'min:8'],
            'status'   => ['required', 'in:active,pending,inactive,suspended'],
            'faculty_id' => [Rule::requiredIf(fn () => Role::find($request->integer('role_id'))?->slug === Role::DEAN), 'nullable', 'exists:faculties,id'],
        ]);

        $user = User::create([
            'role_id'   => $validated['role_id'],
            'faculty_id' => $validated['faculty_id'] ?? null,
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'password'  => Hash::make($validated['password']),
            'is_active' => $validated['status'] === 'active',
            'status'    => $validated['status'],
        ]);

        return redirect()->route('users.index')
            ->with('success', "User account for '{$user->name}' created successfully.");
    }

    public function show(User $user)
    {
        $this->ensureNonStaff($user);
        $user->load(['role', 'student.schoolClass', 'teacher.classTeacherOf', 'guardian.students']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->ensureNonStaff($user);
        $roles = Role::where('slug', '!=', Role::STAFF)->get();
        return view('users.edit', ['user' => $user, 'roles' => $roles, 'faculties' => Faculty::where('is_active', true)->orderBy('name')->get()]);
    }

    public function update(Request $request, User $user)
    {
        $this->ensureNonStaff($user);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'role_id'  => [
                'required',
                Rule::exists('roles', 'id')->where('slug', '!=', Role::STAFF),
            ],
            'status'   => ['required', 'in:active,pending,inactive,suspended'],
            'password' => ['nullable', 'string', 'min:8'],
            'faculty_id' => [Rule::requiredIf(fn () => Role::find($request->integer('role_id'))?->slug === Role::DEAN), 'nullable', 'exists:faculties,id'],
        ]);

        if (!auth()->user()->isSuperAdmin() && Role::find($validated['role_id'])?->slug === Role::SUPER_ADMIN) {
            abort(403, 'Only a Super Admin can assign the Super Admin role.');
        }

        $user->name      = $validated['name'];
        $user->email     = $validated['email'];
        $user->phone     = $validated['phone'];
        $user->role_id   = $validated['role_id'];
        $user->faculty_id = $validated['faculty_id'] ?? null;
        $user->status    = $validated['status'];
        $user->is_active = $validated['status'] === 'active';

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', "User account for '{$user->name}' updated successfully.");
    }

    public function activate(User $user)
    {
        $this->ensureNonStaff($user);
        $user->update(['status' => 'active', 'is_active' => true]);
        return back()->with('success', "User '{$user->name}' is now active.");
    }

    public function deactivate(User $user)
    {
        $this->ensureNonStaff($user);
        $user->update(['status' => 'inactive', 'is_active' => false]);
        return back()->with('success', "User '{$user->name}' has been deactivated.");
    }

    public function suspend(User $user)
    {
        $this->ensureNonStaff($user);
        $user->update(['status' => 'suspended', 'is_active' => false]);
        return back()->with('success', "User '{$user->name}' has been suspended.");
    }

    public function approve(User $user)
    {
        $this->ensureNonStaff($user);
        $user->update(['status' => 'active', 'is_active' => true]);
        return back()->with('success', "Registration for '{$user->name}' has been approved and activated.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->ensureNonStaff($user);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Password for '{$user->name}' has been reset successfully.");
    }

    public function destroy(User $user)
    {
        $this->ensureNonStaff($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own authenticated administrator account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$name}' deleted successfully.");
    }

    private function ensureNonStaff(User $user): void
    {
        abort_unless($user->role?->slug !== Role::STAFF, 404);
    }
}
