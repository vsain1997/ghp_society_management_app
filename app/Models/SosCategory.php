<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosCategory extends Model
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

    public function sosAlerts()
    {
        return $this->hasMany(related: Sos::class, foreignKey: 'sos_category_id', localKey: 'id');
    }

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

    public function emergencyDetails()
    {
        return $this->hasMany(SosCategoryEmergencyDetail::class);
    }
}
