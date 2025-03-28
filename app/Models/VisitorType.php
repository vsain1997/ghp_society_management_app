<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

}
