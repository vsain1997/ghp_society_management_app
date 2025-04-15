<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class BillPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bill_id',
        'txn_id',
        'orderId',
        'amount',
        'tax',
        'fee',
        'status',
        'payment_mood',
        'extra_details'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(fn($u) => $u->uuid = Uuid::uuid4());
    }

    protected $casts = [
        'extra_details' => 'array',
    ];
}
