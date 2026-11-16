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

    public function member(): BelongsTo{
        return $this->belongsTo(Member::class);
    }

    public function membershipLevel(): BelongsTo{
        return $this->belongsTo(MembershipLevel::class);
    }

    public function payments(): HasMany{
        return $this->hasMany(Payment::class);
    }
}
