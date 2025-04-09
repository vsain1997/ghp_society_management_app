<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class BillWhatsappLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bill_id',
        'status',
        'details',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(fn($u) => $u->uuid = Uuid::uuid4());
    }

    protected $cast= [
        'details' => 'array'
    ];
}
