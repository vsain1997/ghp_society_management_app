<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'status',
        'floors',
        'floor_units',
        'member_id',
        'city',
        'state',
        'pin',
        'contact',
        'email',
        'registration_num',
        'type',
        'total_area',
        'total_towers',
        'amenities',
    ];

    protected $dates = ['deleted_at'];//treat as date

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
    public function society_contacts()
    {
        return $this->hasMany(SocietyContact::class);
    }
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function assigned_admin()
    {
        return $this->belongsTo(related: Member::class, foreignKey: 'member_id', ownerKey: 'user_id');
    }

    // Define a scope for society_search filtering
    public function scopeSearchByName($query, $societyName)
    {
        if ($societyName) {
            return $query->where('name', 'LIKE', '%' . $societyName . '%');
        }
        return $query;
    }

    // Define a scope for status filtering
    public function scopeSearchByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', '=', $status);
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

    public function notices()
    {
        return $this->hasMany(Notice::class, 'society_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'society_id');
    }

    public function visitors()
    {
        return $this->hasMany(Event::class, 'society_id');
    }

    public function residentDocuments()
    {
        return $this->hasMany(Document::class, 'society_id');
    }

    public function polls()
    {
        return $this->hasMany(Poll::class, 'society_id');
    }

    public function referProperties()
    {
        return $this->hasMany(ReferProperty::class, 'society_id');
    }

    public function residentBills()
    {
        return $this->hasMany(Bill::class, 'society_id');
    }

    public function TradeProperty()
    {
        return $this->hasMany(TradeProperty::class, 'society_id');
    }

    public function sosAlerts()
    {
        return $this->hasMany(TradeProperty::class, 'society_id');
    }

    public function serviceProviders()
    {
        return $this->hasMany(ServiceProviders::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'society_id');
    }

    public function residentCallRequests()
    {
        return $this->hasMany(CallbackRequest::class, 'society_id');
    }

    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }

    public function activityLog()
    {
        return $this->hasMany(ActivityLog::class);
    }

}
