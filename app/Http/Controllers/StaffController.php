<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
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
        $staffRoleId = Role::where('slug', Role::STAFF)->value('id');

        $query = User::with('role')->where('role_id', $staffRoleId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role_id', $staffRoleId)->count(),
            'active' => User::where('role_id', $staffRoleId)->where('status', 'active')->count(),
            'pending' => User::where('role_id', $staffRoleId)->where('status', 'pending')->count(),
            'suspended' => User::where('role_id', $staffRoleId)->where('status', 'suspended')->count(),
        ];

        return view('staff.index', compact('users', 'stats'));
    }

    public function create()
    {
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();

        return view('staff.create', compact('staffRole'));
    }

    public function show(User $user)
    {
        $this->ensureStaff($user);
        $user->load('role');

        return view('staff.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->ensureStaff($user);

        return view('staff.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureStaff($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,pending,inactive,suspended'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'is_active' => $validated['status'] === 'active',
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('staff.index')
            ->with('success', "Staff account for '{$user->name}' updated successfully.");
    }

    private function ensureStaff(User $user): void
    {
        abort_unless($user->role?->slug === Role::STAFF, 404);
    }

    public function store(Request $request)
    {
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:active,pending,inactive,suspended'],
        ]);

        $user = User::create([
            'role_id' => $staffRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['status'] === 'active',
            'status' => $validated['status'],
        ]);

        return redirect()->route('staff.index')
            ->with('success', "Staff account for '{$user->name}' created successfully.");
    }
}
