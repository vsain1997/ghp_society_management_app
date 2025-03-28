<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TradeProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'type',
        'floor',
        'unit_type',
        'unit_number',
        'bhk',
        'area',

        'rent_per_month',
        'security_deposit',

        'house_price',
        'upfront',

        'available_from_date',
        'amenities',
        'name',
        'phone',
        'email',
        'society_id',
        'created_by',//resident
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(TradePropertiesFile::class);
    }

    public function scopeSearchById($query, $id)
    {
        if ($id) {
            return $query->where('id', '=', $id);
        }
        return $query;
    }

    public function scopeSearchBySocietyId($query, $society_id)
    {
        if ($society_id) {
            return $query->where('society_id', '=', $society_id);
        }
        return $query;
    }

    public function scopeSearchByResident($query, $created_by)
    {
        if ($created_by) {
            return $query->where('created_by', '=', $created_by);
        }
        return $query;
    }

    public function scopeSearchByOtherResidentExceptMe($query, $created_by)
    {
        if ($created_by) {
            return $query->where('created_by', '!=', $created_by);
        }
        return $query;
    }

    public function scopeSearchByType($query, $id)
    {
        if ($id) {
            return $query->where('type', '=', $id);
        }
        return $query;
    }

    public function getAmenitiesAttribute($value)
    {
        $value = str_replace(['[', ']', '"'], '', $value);
        $value = explode(',', $value);
        // Transform to the desired array of objects
        return array_map(function ($amenity) {
            return ['name' => $amenity];
        }, (array) $value);
    }

    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        // Dynamically adjust the hidden fields based on the 'type'
        // static::retrieved(function ($tradeProperty) {
        //     if ($tradeProperty->type === 'rent') {
        //         // Hide 'house_price' and 'upfront' for rent
        //         $tradeProperty->makeHidden(['house_price', 'upfront']);
        //     } elseif ($tradeProperty->type === 'sell') {
        //         // Hide 'rent_per_month' and 'security_deposit' for sell
        //         $tradeProperty->makeHidden(['rent_per_month', 'security_deposit']);
        //     }
        // });
    }
}
