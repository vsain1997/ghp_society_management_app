<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'user_id',
        'user_of_system',
        'society_id'
    ];

    protected $hidden = [
        'society_id',
        'created_at',
        'updated_at',
    ];

}
