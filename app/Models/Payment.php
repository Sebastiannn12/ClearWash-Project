<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymetsFactory> */
    use HasFactory;

    public function membershipTransaction(): BelongsTo
    {
        return $this->belongsTo(MembershipTransaction::class);
    }
}
