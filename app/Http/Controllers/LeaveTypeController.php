<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    private function authorizeHR(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isHROfficer()), 403, 'HR access required.');
    }

    public function index()
    {
        $this->authorizeHR();

        $leaveTypes = LeaveType::withCount('leaveRequests')->orderBy('name')->paginate(15);

        return view('hr.leave-types.index', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:leave_types,name'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'days_allowed' => ['required', 'integer', 'min:0', 'max:365'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        LeaveType::create($validated);

        return back()->with('success', 'Leave type created successfully.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:leave_types,name,' . $leaveType->id],
            'description'  => ['nullable', 'string', 'max:1000'],
            'days_allowed' => ['required', 'integer', 'min:0', 'max:365'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $leaveType->update($validated);

        return back()->with('success', 'Leave type updated successfully.');
    }
}
