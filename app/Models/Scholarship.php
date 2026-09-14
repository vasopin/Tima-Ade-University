<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'criteria',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function awards(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(ScholarshipAward::class); }
}
