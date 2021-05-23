<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'currency',
        'sender_account_number',
        'receiver_account_number',
        'purpose'
    ];

    protected $hidden = [

    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }
}
