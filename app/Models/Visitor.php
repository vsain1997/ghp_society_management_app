<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_of_visitor',
        'visiting_frequency',
        'visitor_name',
        'phone',
        'no_of_visitors',
        'date',
        'time',
        'vehicle_number',
        'purpose_of_visit',
        'valid_till',
        'status',
        'society_id',
        'user_id',
        'image',
        'added_by',
        'added_by_role',
        'visitor_classification'
    ];

    public function scopeSearchByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }
    public function scopeSearchByResident($query, $user_id)
    {
        if ($user_id) {
            return $query->where('user_id', '=', $user_id);
        }
        return $query;
    }
    public function scopeSearchByAddedUser($query, $user_id)
    {
        if ($user_id) {
            return $query->where('added_by', '=', $user_id);
        }
        return $query;
    }
    public function scopeSearchById($query, $id)
    {
        if ($id) {
            return $query->where('id', '=', $id);
        }
        return $query;
    }

    /**
     * Scope a query to only include future visiting or visit happening within the next 2 hours today.
     *
     */
    public function scopeUpcoming($query)
    {
        $now = Carbon::now('Asia/Kolkata');
        $prevNow = $now->copy()->subDay();
        $now = $prevNow;//data will show from prevDate to upcomming date
        return $query->where(function ($q) use ($now) {
            // Show visits where the date is in the future
            $q->whereDate('date', '>', $now->toDateString())
                // Or where the visit is today, and the current time is less than 2 hours after the visit time
                ->orWhere(function ($q) use ($now) {
                    $q->whereDate('date', '=', $now->toDateString());
                    // ->whereRaw("CONCAT(date, ' ', time) + INTERVAL 2 HOUR >= ?", [$now->toDateTimeString()]);
                });
        });
    }

    // Define the relationship with Society
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function bulkVisitors()
    {
        return $this->hasMany(VisitorBulk::class);
    }

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

    public function checkinDetails()
    {
        return $this->hasMany(CheckinDetail::class);
    }
    public function visitorFeedback()
    {
        return $this->hasOne(VisitorFeedback::class);
    }

    public function lastCheckinDetail()
    {
        return $this->hasOne(CheckinDetail::class, 'visitor_id')->latest('created_at');
        // return $this->hasOne(CheckinDetail::class, 'visitor_id')->latest('checkout_at');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'user_id', 'user_id');
    }


}
