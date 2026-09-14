<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\ScholarshipAward;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    private function authorizeFinance(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isFinanceOfficer()), 403, 'Finance access required.');
    }

    public function index()
    {
        $this->authorizeFinance();
        $items = Scholarship::orderBy('title')->paginate(20);
        return view('admin.scholarships.index', compact('items'));
    }

    public function create()
    {
        $this->authorizeFinance();
        return view('admin.scholarships.create');
    }

    public function store(Request $request)
    {
        $this->authorizeFinance();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'criteria' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Scholarship::create($data);

        return redirect()->route('admin.scholarships.index')->with('success', 'Scholarship created.');
    }

    public function edit(Scholarship $item)
    {
        $this->authorizeFinance();
        return view('admin.scholarships.edit', compact('item'));
    }

    public function update(Request $request, Scholarship $item)
    {
        $this->authorizeFinance();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'criteria' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $item->update($data);

        return redirect()->route('admin.scholarships.index')->with('success', 'Scholarship updated.');
    }

    public function destroy(Scholarship $item)
    {
        $this->authorizeFinance();
        $item->update(['is_active' => false]);
        return redirect()->route('admin.scholarships.index')->with('success', 'Scholarship archived.');
    }

    public function assign(Request $request, Scholarship $scholarship)
    {
        $this->authorizeFinance();
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'term_id' => ['nullable', 'exists:terms,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
        ]);
        ScholarshipAward::create($data + ['scholarship_id' => $scholarship->id, 'status' => 'pending']);
        return back()->with('success', 'Scholarship award created pending approval.');
    }

    public function approveAward(ScholarshipAward $award)
    {
        $this->authorizeFinance();
        $award->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Scholarship award approved.');
    }
}
