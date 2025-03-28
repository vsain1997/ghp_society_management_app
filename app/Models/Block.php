<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'unit_type',
        'unit_size',
        'unit_qty',
        'name',
        'total_units',
        'society_id',
        'property_number',
        'floor',
        'ownership',
        'bhk',
        'total_floor',
    ];

    protected $dates = ['deleted_at'];//treat as date

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
    public function members()
    {
        return $this->hasMany(Member::class);
        // return $this->hasOne(Member::class);
    }

    public function SosAlerts()
    {
        return $this->hasMany(Member::class);
    }

    public function member_info()
    {
        // return $this->hasMany(Member::class);
        return $this->hasOne(Member::class);
    }

}
