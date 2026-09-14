<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'quote',
        'photo_url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
