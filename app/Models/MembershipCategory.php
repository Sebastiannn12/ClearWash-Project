<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipCategory extends Model
{
    public const REQUIRED_NAMES = ['Student', 'Regular', 'Senior Citizen', 'Corporate'];

    protected $fillable = ['name', 'benefits', 'discount_percentage', 'is_active'];

    protected function casts(): array
    {
        return ['discount_percentage' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class)->withTrashed();
    }

    public function isRequired(): bool
    {
        return in_array($this->name, self::REQUIRED_NAMES, true);
    }
}
