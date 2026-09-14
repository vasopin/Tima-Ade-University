<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisingNote extends Model
{
    protected $fillable = [
        'student_id', 'advisor_id', 'body', 'is_private', 'follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'follow_up_at' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}
