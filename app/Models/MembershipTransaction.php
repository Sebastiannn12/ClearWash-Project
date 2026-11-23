<?php

namespace App\Models;

use Database\Factories\MembershipTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipTransaction extends Model
{
    /** @use HasFactory<MembershipTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'membership_level_id',
        'type',
        'status',
        'amount_due',
        'original_fee',
        'discount_percentage',
        'discount_amount',
        'category_name',
        'level_name',
        'validity_days',
        'notes',
        'approved_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'original_fee' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /** @return array{original_fee: string, discount_percentage: string, discount_amount: string, amount_due: string} */
    public static function pricingFor(MembershipCategory $category, MembershipLevel $level): array
    {
        $feeInCents = (int) round((float) $level->fee * 100);
        $discountBasisPoints = (int) round((float) $category->discount_percentage * 100);
        $discountInCents = (int) round($feeInCents * $discountBasisPoints / 10000);

        return [
            'original_fee' => number_format($feeInCents / 100, 2, '.', ''),
            'discount_percentage' => number_format($discountBasisPoints / 100, 2, '.', ''),
            'discount_amount' => number_format($discountInCents / 100, 2, '.', ''),
            'amount_due' => number_format(($feeInCents - $discountInCents) / 100, 2, '.', ''),
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class)->withTrashed();
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
                .$transaction->created_at->format('Y')
                .'-'
                .str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT);

            $transaction->saveQuietly();
        });
    }
}
