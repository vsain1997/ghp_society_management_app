<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallbackRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'request_by',
        'request_to',
        'society_id',
        'description',
        'aprt_no',
        'status',
    ];


    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Get the user who requested the callback (resident).
     */
    public function requestByUser()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    /**
     * Get the user to whom the request is directed (service provider).
     */
    public function requestToUser()
    {
        return $this->belongsTo(User::class, 'request_to');
    }

    /**
     * Get the society that this callback request is associated with.
     */
    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
