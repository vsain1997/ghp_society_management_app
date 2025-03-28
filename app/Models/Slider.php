<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'title',
        'sub_title',
        'image',
    ];

    protected $hidden = [
        'society_id',
        'created_at',
        'updated_at',
    ];

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

}
