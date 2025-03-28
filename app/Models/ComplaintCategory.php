<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'image',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

    public function complaints()
    {
        return $this->hasMany(related: Complaint::class, foreignKey: 'complaint_category_id', localKey: 'id');
    }

    public function serviceProviders()
    {
        return $this->hasMany(related: Staff::class, foreignKey: 'complaint_category_id', localKey: 'id');
    }

}
