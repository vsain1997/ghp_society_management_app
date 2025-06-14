<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyTerms extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
    ];

    protected $hidden = [
        'society_id',
        'created_at',
        'updated_at',
    ];

}
