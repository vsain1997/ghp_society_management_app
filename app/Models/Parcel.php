<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcelid',
        'parcel_name',
        'no_of_parcel',
        'parcel_type',
        'date',
        'time',
        'parcel_company_name',
        'delivery_agent_image',
        'delivery_name',
        'delivery_phone',
        'parcel_of',
        'entry_by',
        'entry_by_role',
        'entry_at',
        'delivery_option',
        'received_by_role',
        'received_by',
        'received_at',
        'handover_status',
        'handover_to',
        'handover_at',
        'society_id'
    ];


    public function parcelOf()
    {
        return $this->belongsTo(User::class, 'parcel_of');
    }

    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function handoverTo()
    {
        return $this->belongsTo(User::class, 'handover_to');
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function parcelComplaint()
    {
        return $this->hasOne(ParcelComplaint::class, 'parcel_id');
    }
    public function member()
    {
        return $this->belongsTo(Member::class, 'parcel_of', 'user_id');
    }

    public function checkinDetail()
    {
        return $this->hasOne(CheckinDetail::class);
    }

    public function scopeSearchById($query, $id)
    {
        if ($id) {
            return $query->where('id', '=', $id);
        }
        return $query;
    }
    public function getDeliveryAgentImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

}
