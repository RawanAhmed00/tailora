<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
   protected $fillable = [
    'booking_id',
    'amount',
    'currency',
    'status',
    'payment_method',
    'paymob_order_id',
    'paymob_transaction_id',
    'paymob_intention_id',
    'paymob_client_secret',
    'payment_url',
    'paid_at',
];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}