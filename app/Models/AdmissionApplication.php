<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'name', 'email', 'phone', 'program_id', 'grade_interested', 'message', 'education', 'documents', 'status', 'decision_reason', 'decided_by', 'decided_at'];

    protected function casts(): array
    {
        return ['program_id' => 'integer', 'education' => 'array', 'documents' => 'array', 'decided_at' => 'datetime'];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    protected static function booted(): void
    {
        static::creating(function (self $application) {
            $application->reference ??= 'TA-' . now()->format('Ym') . '-' . strtoupper(Str::random(6));
        });
    }
}
