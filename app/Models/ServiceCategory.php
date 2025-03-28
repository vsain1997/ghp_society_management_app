<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function serviceProviders()
    {
        return $this->hasMany(related: ServiceProviders::class, foreignKey: 'service_category_id', localKey: 'id');
    }

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

    public function callbackRequests()
    {
        return $this->hasMany(related: CallbackRequest::class, foreignKey: 'service_category_id', localKey: 'id');
    }

}
