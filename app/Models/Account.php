<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'type',
        'balance',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
