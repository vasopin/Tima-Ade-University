<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ["isbn", "title", "author", "publisher", "publication_year", "category", "total_copies", "available_copies", "price", "description", "is_available"];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }
}
