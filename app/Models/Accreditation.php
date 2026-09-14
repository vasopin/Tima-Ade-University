<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accreditation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'issuer',
        'issued_at',
        'expires_at',
        'logo_url',
        'is_active'
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];
}
