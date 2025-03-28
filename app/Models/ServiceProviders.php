<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviders extends Model
{
    use HasFactory;


    protected $fillable = [
        'service_category_id',
        'name',
        'phone',
        'email',
        'address',
        'society_id',
        'user_id',
    ];

    protected $dates = ['deleted_at'];//treat as date
    public function serviceCategory()
    {
        return $this->belongsTo(related: ServiceCategory::class, foreignKey: 'service_category_id', ownerKey: 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function scopeSearchByName($query, $name)
    {
        if ($name) {
            return $query->where('name', '=', $name);
        }
        return $query;
    }

    public function scopeSearchByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', '=', $status);
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

    public function scopeSearchBySocietyId($query, $society_id)
    {
        if ($society_id) {
            return $query->where('society_id', '=', $society_id);
        }
        return $query;
    }

    public function scopeSearchByCategory($query, $service_category_id)
    {
        if ($service_category_id) {
            return $query->where('service_category_id', '=', $service_category_id);
        }
        return $query;
    }
}
