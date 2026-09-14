<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'type', 'grade_interested', 'message', 'status',
    ];
}
