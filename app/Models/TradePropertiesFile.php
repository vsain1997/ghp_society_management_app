<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradePropertiesFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_property_id',
        'file',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function TradeProperty()
    {
        return $this->belongsTo(TradeProperty::class);
    }

    public function getFileAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

}
