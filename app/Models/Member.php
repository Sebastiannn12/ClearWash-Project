<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'category',
        'membership_level', 'address', 'joined_on', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Member $member): void {
            $member->membership_number = 'CW-' . $member->created_at->format('Y')
                . '-' . str_pad((string) $member->id, 6, '0', STR_PAD_LEFT);
            $member->saveQuietly();
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
