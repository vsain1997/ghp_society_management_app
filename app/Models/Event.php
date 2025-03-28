<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'sub_title',
        'date',
        'time',
        'image',
        'description',
        'status',
        'society_id',
        'created_by',
    ];

    // Define the relationship with Society
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    // Define the relationship with User (created_by)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSearchByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }
    public function scopeSearchBySociety($query, $society_id)
    {
        if ($society_id) {
            return $query->where('society_id', '=', $society_id);
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

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

    /**
     * Scope a query to only include future events or events happening within the next 2 hours today.
     *
     */
    public function scopeUpcoming($query)
    {
        $now = Carbon::now('Asia/Kolkata');

        return $query->where(function ($q) use ($now) {
            // Show events where the date is in the future
            $q->whereDate('date', '>', $now->toDateString())
                // Or where the event is today, and the current time is less than 2 hours after the event time
                ->orWhere(function ($q) use ($now) {
                    $q->whereDate('date', '=', $now->toDateString())
                        ->whereRaw("CONCAT(date, ' ', time) + INTERVAL 2 HOUR >= ?", [$now->toDateTimeString()]);
                });
        });
    }

}
