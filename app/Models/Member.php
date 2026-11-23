<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'category',
        'membership_category_id',
        'membership_level',
        'membership_level_id',
        'address',
        'joined_on',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Member $member): void {
            $member->membership_number = 'CW-'.$member->created_at->format('Y')
                .'-'.str_pad((string) $member->id, 6, '0', STR_PAD_LEFT);
            $member->saveQuietly();
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function membershipTransactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    public function membershipCategory(): BelongsTo
    {
        return $this->belongsTo(MembershipCategory::class);
    }

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }
}
