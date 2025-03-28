<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'event_type',
        'activity_name',
        'description',
        'user_id',
        'user_role',
        'society_id',
        'before_data',
        'after_data',
        'request_data',
        'ip_address',
        'user_agent',
        'route_name',
        'model_type',
        'model_id',
        'status',
        'severity_level',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'before_data' => 'array', // Casting for easy access if JSON support is added
        'after_data' => 'array',
        'request_data' => 'array',
        'severity_level' => 'integer',
    ];

    /**
     * Relationship to the user who performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to the society involved in the activity.
     */
    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id');
    }
}
