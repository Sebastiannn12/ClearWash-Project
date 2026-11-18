<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\MembershipTransactionFactory> */
    use HasFactory;
    protected $fillable = [
        'member_id',
        'membership_level_id',
        'type',
        'status',
        'amount_due',
        'validity_days',
        'notes',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    
    protected static function booted(): void
    {
        static::created(function (MembershipTransaction $transaction): void {
            $transaction->reference_number = 'TXN-'
                . $transaction->created_at->format('Y')
                . '-'
                . str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT);

            $transaction->saveQuietly();
        });
    }
}
