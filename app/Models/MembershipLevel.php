<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipLevel extends Model
{
    public const REQUIRED_NAMES = ['Basic', 'Silver', 'Gold', 'Platinum'];

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

    public function members(): HasMany
    {
        return $this->hasMany(Member::class)->withTrashed();
    }

    public function membershipTransactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    public function isRequired(): bool
    {
        return in_array($this->name, self::REQUIRED_NAMES, true);
    }
}
