<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckinDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'parcel_id', // Add parcel_id
        'status',
        'requested_at',
        'checkin_at',
        'checkout_at',
        'request_by',
        'checkin_by',
        'checkout_by',
        'visitor_of',
        'society_id',
        'by_resident',
        'by_daily_help',
        'daily_help_for_member',
        'checkin_type',
        'checkout_type'
    ];

    /**
     * Relationship with Visitor model.
     */
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Relationship with Parcel model.
     */
    public function parcel()
    {
        return $this->belongsTo(Parcel::class); // Add this relationship
    }

    /**
     * Relationship with User model for 'request_by'.
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    /**
     * Relationship with User model for 'checkin_by'.
     */
    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checkin_by');
    }

    /**
     * Relationship with User model for 'checkout_by'.
     */
    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checkout_by');
    }

    /**
     * Relationship with User model for 'visitor_of'.
     */
    public function visitorOf()
    {
        return $this->belongsTo(User::class, 'visitor_of');
    }

    /**
     * Relationship with Society model.
     */
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function resident()
    {
        return $this->belongsTo(User::class, 'by_resident');
    }

    public function dailyHelp()
    {
        return $this->belongsTo(User::class, 'by_daily_help');
    }

    public function dailyHelpMemberDetails()
    {
        return $this->belongsTo(User::class, 'daily_help_for_member');
    }
}
