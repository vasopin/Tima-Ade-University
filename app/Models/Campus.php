<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'description', 'address', 'phone', 'email', 'is_active'];

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function faculties(): HasMany { return $this->hasMany(Faculty::class); }
}
