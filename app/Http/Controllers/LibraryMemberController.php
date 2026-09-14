<?php

namespace App\Http\Controllers;

use App\Models\LibraryMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LibraryMemberController extends Controller
{
    private function authorizeLibrarian(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isLibrarian()), 403, 'Librarian access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeLibrarian();

        $query = LibraryMember::with('user');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        return view('library.members.index', compact('members'));
    }

    public function create()
    {
        $this->authorizeLibrarian();

        $users = User::whereDoesntHave('libraryMember')
            ->whereHas('role', fn ($q) => $q->where('slug', '!=', Role::STAFF))
            ->orderBy('name')->get();

        return view('library.members.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizeLibrarian();

        $validated = $request->validate([
            'user_id'                => ['required', 'exists:users,id', Rule::unique('library_members', 'user_id')],
            'membership_type'        => ['required', 'in:student,staff,teacher,external'],
            'membership_start_date'  => ['required', 'date'],
            'membership_end_date'    => ['nullable', 'date', 'after:membership_start_date'],
        ]);

        $validated['is_active'] = true;

        LibraryMember::create($validated);

        return redirect()->route('library.members.index')->with('success', 'Library membership created for the existing user account.');
    }

    public function deactivate(LibraryMember $member)
    {
        $this->authorizeLibrarian();

        abort_if($member->borrowings()->where('status', 'active')->exists(), 422, 'Cannot deactivate a member with active borrowings.');

        $member->update(['is_active' => false]);

        return back()->with('success', 'Library membership deactivated.');
    }

    public function activate(LibraryMember $member)
    {
        $this->authorizeLibrarian();

        $member->update(['is_active' => true]);

        return back()->with('success', 'Library membership reactivated.');
    }
}
