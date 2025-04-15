<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
        'bill_type',
        'amount',
        'due_date',
        'society_id',
        'created_by',
        'invoice_number',
        'status',
        'payment_status',
        'payment_date'
    ];

    // protected $appends = ['due_date_remain_days', 'due_date_delay_days'];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate a unique invoice number when creating a bill
        static::creating(function ($model) {
            $model->invoice_number = 'INV-' . Str::upper(Str::random(10));
        });

        // --------------------------------------------

        static::addGlobalScope('due_dates', function (Builder $builder) {
            $builder->selectRaw("
                *,
                CASE
                    WHEN CURRENT_DATE <= due_date THEN DATEDIFF(due_date, CURRENT_DATE)
                    ELSE 0
                END AS due_date_remain_days,
                CASE
                    WHEN CURRENT_DATE > due_date THEN ABS(DATEDIFF(due_date, CURRENT_DATE))
                    ELSE 0
                END AS due_date_delay_days
            ");
        });

        // static::retrieved(function ($bill) {
        //     $dueDate = Carbon::parse($bill->due_date);
        //     $currentDate = Carbon::now('Asia/Kolkata');
        //     if ($currentDate < $dueDate) {
        //         $currentDate = Carbon::now('Asia/Kolkata')->startOfDay();
        //         $dueDate = Carbon::parse($bill->due_date)->startOfDay();

        //     } elseif ($currentDate > $dueDate) {
        //         $dueDate = Carbon::parse($bill->due_date)->startOfDay();
        //         $currentDate = Carbon::now('Asia/Kolkata')->endOfDay();

        //     }

        //     // Calculate the difference in days
        //     $remainDays = $currentDate->diffInDays($dueDate, false); // false for negative if overdue
        //     // $remainDays = $dueDate->diffInDays($currentDate, false); // false for negative if overdue

        //     // Assign remain days: if future date, keep; if past, set to 0
        //     $bill->attributes['due_date_remain_days'] = $remainDays >= 0 ? $remainDays : 0;

        //     // Assign delay days: if past date, set the positive delay days, else 0
        //     $bill->attributes['due_date_delay_days'] = $remainDays < 0 ? abs($remainDays) : 0;

        //     // // Assign remain days
        //     // $bill->attributes['due_date_remain_days'] = $remainDays < 0 ? 0 : $remainDays;

        //     // // Assign delay days
        //     // $bill->attributes['due_date_delay_days'] = $remainDays < 0 ? abs($remainDays) : 0;
        // });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function service()
    {
        return $this->belongsTo(BillService::class);
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function scopeSearchBySociety($query, $society_id)
    {
        if ($society_id) {
            return $query->where('society_id', '=', $society_id);
        }
        return $query;
    }
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
    public function scopeSearchById($query, $id)
    {
        if ($id) {
            return $query->where('id', '=', $id);
        }
        return $query;
    }

    public function scopeSearchByBillType($query, $bill_type)
    {
        if ($bill_type) {
            return $query->where('bill_type', '=', $bill_type);
        }
        return $query;
    }

    // Accessors for due_date_remain_days and due_date_delay_days (if you want to include them explicitly)
    public function getDueDateRemainDaysAttribute()
    {
        return $this->attributes['due_date_remain_days'];
    }

    public function getDueDateDelayDaysAttribute()
    {
        return $this->attributes['due_date_delay_days'];
    }

}
