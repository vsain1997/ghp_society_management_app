<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitingFrequency extends Model
{
    use HasFactory;

    protected $fillable = [
        'frequency',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

}
