<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosCategoryEmergencyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'type',
        'sos_category_id',
        'society_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function sosCategory()
    {
        return $this->belongsTo(SosCategory::class);
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
