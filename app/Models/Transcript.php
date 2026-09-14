<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transcript extends Model
{
    protected $fillable = ['student_id', 'academic_year', 'term', 'gpa', 'total_credits', 'summary', 'generated_by', 'generated_at'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}

