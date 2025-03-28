<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferProperty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'min_budget',
        'max_budget',
        'location',
        'unit_type',
        'bhk',
        'property_status',
        'property_fancing',
        'remark',
        'society_id',
        'user_id'
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearchByResident($query, $user_id)
    {
        if ($user_id) {
            return $query->where('user_id', '=', $user_id);
        }
        return $query;
    }

    public function scopeSearchById($query, $id)
    {
        if ($id) {
            return $query->where('id', '=', $id);
        }
        return $query;
    }

}
