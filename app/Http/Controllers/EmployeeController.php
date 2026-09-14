<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    private function authorizeHR(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isHROfficer()), 403, 'HR access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeHR();

        $query = Employee::with('user');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhere('employee_id', 'like', "%{$search}%");
        }

        if ($request->filled('department')) {
            $query->where('department', $request->string('department'));
        }

        $employees = $query->latest()->paginate(15)->withQueryString();

        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $this->authorizeHR();

        $users = User::whereDoesntHave('employee')
            ->whereHas('role', fn ($q) => $q->where('slug', '!=', Role::STUDENT))
            ->orderBy('name')->get();

        return view('hr.employees.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'user_id'          => ['required', 'exists:users,id', Rule::unique('employees', 'user_id')],
            'department'       => ['nullable', 'string', 'max:255'],
            'designation'      => ['nullable', 'string', 'max:255'],
            'date_of_joining'  => ['nullable', 'date'],
            'employment_type'  => ['required', 'in:full_time,part_time,contract,temporary'],
            'base_salary'      => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['employee_id'] = 'EMP-' . str_pad((string) (Employee::max('id') + 1), 5, '0', STR_PAD_LEFT);

        Employee::create($validated);

        return redirect()->route('hr.employees.index')->with('success', 'Employee record created and linked to the existing user account.');
    }

    public function show(Employee $employee)
    {
        $this->authorizeHR();

        $employee->load(['user', 'leaveRequests.leaveType', 'payrollRecords.payrollPeriod']);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeHR();

        $employee->load('user');

        return view('hr.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'department'       => ['nullable', 'string', 'max:255'],
            'designation'      => ['nullable', 'string', 'max:255'],
            'date_of_joining'  => ['nullable', 'date'],
            'date_of_leaving'  => ['nullable', 'date'],
            'employment_type'  => ['required', 'in:full_time,part_time,contract,temporary'],
            'base_salary'      => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:2000'],
        ]);

        $employee->update($validated);

        return redirect()->route('hr.employees.index')->with('success', 'Employee record updated successfully.');
    }
}
