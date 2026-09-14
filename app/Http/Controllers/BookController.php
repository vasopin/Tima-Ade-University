<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function authorizeLibrarian(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isLibrarian()), 403, 'Librarian access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeLibrarian();

        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $books = $query->orderBy('title')->paginate(15)->withQueryString();

        return view('library.books.index', compact('books'));
    }

    public function create()
    {
        $this->authorizeLibrarian();

        return view('library.books.create');
    }

    public function store(Request $request)
    {
        $this->authorizeLibrarian();

        $validated = $request->validate([
            'isbn'             => ['nullable', 'string', 'max:32', 'unique:books,isbn'],
            'title'            => ['required', 'string', 'max:255'],
            'author'           => ['nullable', 'string', 'max:255'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'digits:4', 'integer', 'min:1500'],
            'category'         => ['nullable', 'string', 'max:100'],
            'total_copies'     => ['required', 'integer', 'min:1'],
            'price'            => ['nullable', 'numeric', 'min:0'],
            'description'      => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['available_copies'] = $validated['total_copies'];
        $validated['is_available'] = true;

        Book::create($validated);

        return redirect()->route('library.books.index')->with('success', 'Book added to the catalog.');
    }

    public function edit(Book $book)
    {
        $this->authorizeLibrarian();

        return view('library.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorizeLibrarian();

        $validated = $request->validate([
            'isbn'             => ['nullable', 'string', 'max:32', 'unique:books,isbn,' . $book->id],
            'title'            => ['required', 'string', 'max:255'],
            'author'           => ['nullable', 'string', 'max:255'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'digits:4', 'integer', 'min:1500'],
            'category'         => ['nullable', 'string', 'max:100'],
            'total_copies'     => ['required', 'integer', 'min:1'],
            'price'            => ['nullable', 'numeric', 'min:0'],
            'description'      => ['nullable', 'string', 'max:2000'],
        ]);

        $borrowedOut = $book->total_copies - $book->available_copies;
        abort_if($validated['total_copies'] < $borrowedOut, 422, "Cannot set total copies below the {$borrowedOut} currently borrowed.");

        $validated['available_copies'] = $validated['total_copies'] - $borrowedOut;
        $validated['is_available'] = $validated['available_copies'] > 0;

        $book->update($validated);

        return redirect()->route('library.books.index')->with('success', 'Book catalog entry updated.');
    }

    public function destroy(Book $book)
    {
        $this->authorizeLibrarian();

        abort_if($book->borrowings()->where('status', 'active')->exists(), 422, 'Cannot delete a book with active borrowings.');

        $book->delete();

        return redirect()->route('library.books.index')->with('success', 'Book removed from the catalog.');
    }
}
