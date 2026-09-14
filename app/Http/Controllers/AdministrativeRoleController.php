<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdministrativeRoleController extends Controller
{
    private const ROLE_SLUGS = [
        Role::ADMISSIONS_OFFICER,
        Role::FINANCE_OFFICER,
        Role::REGISTRAR,
        Role::HR_OFFICER,
        Role::LIBRARIAN,
        Role::PRESIDENT,
        Role::CHANCELLOR,
        Role::DEAN,
        Role::DEPARTMENT_HEAD,
        Role::ACADEMIC_ADVISOR,
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Unauthorized. Administrative privileges required.');
            return $next($request);
        });
    }

    public function index(Request $request, string $role)
    {
        $roleModel = $this->role($role);
        $query = User::with('role')->where('role_id', $roleModel->id);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $stats = [
            'total' => User::where('role_id', $roleModel->id)->count(),
            'active' => User::where('role_id', $roleModel->id)->where('status', 'active')->count(),
            'pending' => User::where('role_id', $roleModel->id)->where('status', 'pending')->count(),
            'suspended' => User::where('role_id', $roleModel->id)->where('status', 'suspended')->count(),
        ];

        return view('administrative-roles.index', compact('users', 'stats', 'roleModel', 'role'));
    }

    public function create(string $role)
    {
        $roleModel = $this->role($role);
        return view('administrative-roles.form', compact('roleModel', 'role'));
    }

    public function store(Request $request, string $role)
    {
        $roleModel = $this->role($role);
        $validated = $this->validateUser($request);
        $user = User::create([
            'role_id' => $roleModel->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['status'] === 'active',
            'status' => $validated['status'],
        ]);

        return redirect()->route($role . '.index')->with('success', "{$roleModel->name} account for '{$user->name}' created successfully.");
    }

    public function show(User $user, string $role)
    {
        $roleModel = $this->role($role);
        $this->ensureRole($user, $roleModel);
        return view('administrative-roles.show', compact('user', 'roleModel', 'role'));
    }

    public function edit(User $user, string $role)
    {
        $roleModel = $this->role($role);
        $this->ensureRole($user, $roleModel);
        return view('administrative-roles.form', compact('user', 'roleModel', 'role'));
    }

    public function update(Request $request, User $user, string $role)
    {
        $roleModel = $this->role($role);
        $this->ensureRole($user, $roleModel);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,pending,inactive,suspended'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'is_active' => $validated['status'] === 'active',
        ]);
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route($role . '.index')->with('success', "{$roleModel->name} account updated successfully.");
    }

    public function destroy(User $user, string $role)
    {
        $roleModel = $this->role($role);
        $this->ensureRole($user, $roleModel);
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own authenticated administrator account.');
        $user->delete();
        return redirect()->route($role . '.index')->with('success', "{$roleModel->name} account deleted successfully.");
    }

    private function validateUser(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:active,pending,inactive,suspended'],
        ]);
    }

    private function role(string $slug): Role
    {
        abort_unless(in_array($slug, self::ROLE_SLUGS, true), 404);

        $names = [
            Role::ADMISSIONS_OFFICER => 'Admissions Officer',
            Role::FINANCE_OFFICER => 'Finance Officer',
            Role::REGISTRAR => 'Registrar',
            Role::HR_OFFICER => 'HR Officer',
            Role::LIBRARIAN => 'Librarian',
            Role::PRESIDENT => 'President',
            Role::CHANCELLOR => 'Chancellor',
            Role::DEAN => 'Dean',
            Role::DEPARTMENT_HEAD => 'Department Head',
            Role::ACADEMIC_ADVISOR => 'Academic Advisor',
        ];

        return Role::firstOrCreate(
            ['slug' => $slug],
            ['name' => $names[$slug], 'description' => "{$names[$slug]} access"]
        );
    }

    private function ensureRole(User $user, Role $role): void
    {
        abort_unless($user->role_id === $role->id, 404);
    }
}