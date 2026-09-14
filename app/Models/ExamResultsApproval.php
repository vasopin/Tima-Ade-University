<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResultsApproval extends Model
{
    protected $table = 'exam_results_approval';

    protected $fillable = ['exam_id', 'approved_by', 'status', 'notes', 'approved_at', 'published_at', 'published_by'];

    protected $casts = [
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function publishedBy(): BelongsTo { return $this->belongsTo(User::class, 'published_by'); }
}
