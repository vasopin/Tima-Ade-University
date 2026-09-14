<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['faculty_id', 'head_user_id', 'name', 'code', 'description', 'is_active'];

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
    public function head(): BelongsTo { return $this->belongsTo(User::class, 'head_user_id'); }
    public function programs(): HasMany { return $this->hasMany(Program::class); }
    public function subjects(): HasMany { return $this->hasMany(Subject::class); }
}
