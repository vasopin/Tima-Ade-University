<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
    protected $fillable = ['assignment_id', 'teacher_id', 'name', 'description'];
    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
    public function criteria(): HasMany { return $this->hasMany(RubricCriterion::class)->orderBy('sort_order'); }
}
