<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryMember extends Model
{
    protected $fillable = ["user_id", "membership_type", "membership_start_date", "membership_end_date", "is_active"];

    protected $casts = ["membership_start_date" => "date", "membership_end_date" => "date"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }
}
