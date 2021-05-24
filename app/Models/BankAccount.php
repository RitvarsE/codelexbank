<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'currency',
        'amount',
        'deposit',
        'withdraw',
        'user_id',
        'number',
        'type',
        'tax'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }
}
