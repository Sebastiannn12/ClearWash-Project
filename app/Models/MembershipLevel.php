<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    protected $fillable = [
        'name',
        'fee',
        'validity_days',
        'benefits',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
