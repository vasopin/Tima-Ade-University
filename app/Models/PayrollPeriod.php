<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    protected $fillable = ['period_name', 'start_date', 'end_date', 'is_active', 'is_closed'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function payrollRecords(): HasMany
    {
        return $this->hasMany(PayrollRecord::class);
    }
}
