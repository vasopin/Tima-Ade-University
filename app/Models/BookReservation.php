<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReservation extends Model
{
    protected $fillable = ["library_member_id", "book_id", "reservation_date", "status", "fulfilled_at"];

    protected $casts = ["reservation_date" => "date", "fulfilled_at" => "datetime"];

    public function libraryMember(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
