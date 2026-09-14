<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveClass extends Model
{
    protected $table = 'live_classes';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'school_class_id',
        'title',
        'scheduled_at',
        'duration_minutes',
        'started_at',
        'ended_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LiveClassParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveClassMessage::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LiveClassActivity::class)->latest();
    }
}
