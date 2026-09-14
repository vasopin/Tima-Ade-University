<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\LibraryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    private function authorizeLibrarian(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isLibrarian()), 403, 'Librarian access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeLibrarian();

        $query = Borrowing::with(['libraryMember.user', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $borrowings = $query->latest('borrow_date')->paginate(15)->withQueryString();
        $activeMembers = LibraryMember::where('is_active', true)->with('user')->get();
        $availableBooks = Book::where('available_copies', '>', 0)->orderBy('title')->get();

        return view('library.borrowings.index', compact('borrowings', 'activeMembers', 'availableBooks'));
    }

    /**
     * Issue a book. Uses a DB transaction with a row lock so concurrent
     * borrow requests cannot both succeed when only one copy remains.
     */
    public function store(Request $request)
    {
        $this->authorizeLibrarian();

        $validated = $request->validate([
            'library_member_id' => ['required', 'exists:library_members,id'],
            'book_id'           => ['required', 'exists:books,id'],
            'due_date'          => ['required', 'date', 'after:today'],
        ]);

        $member = LibraryMember::findOrFail($validated['library_member_id']);
        abort_unless($member->is_active, 422, 'This library membership is not active.');

        DB::transaction(function () use ($validated) {
            $book = Book::lockForUpdate()->findOrFail($validated['book_id']);

            abort_if($book->available_copies < 1, 422, "No available copies of '{$book->title}' remain — borrowing is blocked.");

            $book->decrement('available_copies');
            $book->update(['is_available' => $book->available_copies > 0]);

            Borrowing::create([
                'library_member_id' => $validated['library_member_id'],
                'book_id'           => $validated['book_id'],
                'borrow_date'       => now()->toDateString(),
                'due_date'          => $validated['due_date'],
                'status'            => 'active',
            ]);
        });

        return back()->with('success', 'Book issued successfully.');
    }

    /**
     * Return a book. Restores available_copies within a transaction to
     * keep the count consistent with the borrowings ledger.
     */
    public function returnBook(Borrowing $borrowing)
    {
        $this->authorizeLibrarian();

        DB::transaction(function () use ($borrowing) {
            $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);
            abort_unless($borrowing->status === 'active' || $borrowing->status === 'overdue', 422, 'This book has already been returned.');
            $borrowing->update(['status' => 'returned', 'return_date' => now()->toDateString()]);

            $book = Book::lockForUpdate()->findOrFail($borrowing->book_id);
            $book->increment('available_copies');
            $book->update(['is_available' => true]);
        });

        return back()->with('success', 'Book marked as returned.');
    }

    public function markOverdue()
    {
        $this->authorizeLibrarian();

        Borrowing::where('status', 'active')->where('due_date', '<', now()->toDateString())->update(['status' => 'overdue']);

        return back()->with('success', 'Overdue borrowings refreshed based on current due dates.');
    }
}
