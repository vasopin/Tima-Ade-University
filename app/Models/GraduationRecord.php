<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduationRecord extends Model
{
    protected $fillable = ["student_id", "graduation_date", "diploma_number", "status", "degree_name", "honors", "approved_by", "approved_at"];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "approved_by");
    }
}
