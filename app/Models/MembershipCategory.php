<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipCategory extends Model
{
    protected $fillable = ['name', 'benefits', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
