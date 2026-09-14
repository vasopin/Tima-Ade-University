<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReservation;
use App\Models\LibraryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookReservationController extends Controller
{
    private function authorizeLibrarian(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isLibrarian()), 403, 'Librarian access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeLibrarian();

        $query = BookReservation::with(['libraryMember.user', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $reservations = $query->latest('reservation_date')->paginate(15)->withQueryString();
        $activeMembers = LibraryMember::where('is_active', true)->with('user')->get();
        $books = Book::orderBy('title')->get();

        return view('library.reservations.index', compact('reservations', 'activeMembers', 'books'));
    }

    public function store(Request $request)
    {
        $this->authorizeLibrarian();

        $validated = $request->validate([
            'library_member_id' => ['required', 'exists:library_members,id'],
            'book_id'           => ['required', 'exists:books,id'],
        ]);

        $member = LibraryMember::findOrFail($validated['library_member_id']);
        abort_unless($member->is_active, 422, 'This library membership is not active.');
        abort_if(
            BookReservation::where('library_member_id', $validated['library_member_id'])
                ->where('book_id', $validated['book_id'])
                ->where('status', 'active')
                ->exists(),
            422,
            'This member already has an active reservation for this book.'
        );

        BookReservation::create([
            'library_member_id' => $validated['library_member_id'],
            'book_id'           => $validated['book_id'],
            'reservation_date'  => now()->toDateString(),
            'status'            => 'active',
        ]);

        return back()->with('success', 'Reservation created.');
    }

    /**
     * Fulfil a reservation by issuing the book, only if a copy is free.
     * Wrapped in a transaction so the copy count and borrowing ledger stay consistent.
     */
    public function fulfil(BookReservation $reservation)
    {
        $this->authorizeLibrarian();

        abort_unless($reservation->status === 'active', 422, 'Only active reservations can be fulfilled.');

        DB::transaction(function () use ($reservation) {
            $book = Book::lockForUpdate()->findOrFail($reservation->book_id);
            abort_if($book->available_copies < 1, 422, "No available copies of '{$book->title}' remain to fulfil this reservation.");

            $book->decrement('available_copies');
            $book->update(['is_available' => $book->available_copies > 0]);

            \App\Models\Borrowing::create([
                'library_member_id' => $reservation->library_member_id,
                'book_id'           => $reservation->book_id,
                'borrow_date'       => now()->toDateString(),
                'due_date'          => now()->addDays(14)->toDateString(),
                'status'            => 'active',
            ]);

            $reservation->update(['status' => 'fulfilled', 'fulfilled_at' => now()]);
        });

        return back()->with('success', 'Reservation fulfilled and book issued to the member.');
    }

    public function cancel(BookReservation $reservation)
    {
        $this->authorizeLibrarian();

        abort_unless($reservation->status === 'active', 422, 'Only active reservations can be cancelled.');

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation cancelled.');
    }
}
