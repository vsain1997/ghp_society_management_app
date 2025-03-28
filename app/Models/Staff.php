<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    //table name
    protected $table = 'staffs';

    protected $fillable = [
        'complaint_category_id',
        'role',
        'name',
        'status',
        'phone',
        'email',
        'address',
        'card_type',
        'card_number',
        'card_file',
        'society_id',
        'user_id',
    ];

    protected $dates = ['deleted_at'];//treat as date
    public function staffCategory()
    {
        return $this->belongsTo(related: ComplaintCategory::class, foreignKey: 'complaint_category_id', ownerKey: 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'complaint_category_id', 'complaint_category_id');
    }


    public function scopeSearchByName($query, $name)
    {
        if ($name) {
            return $query->where('name', '=', $name);
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

    public function scopeSearchByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }

    public function scopeSearchBySocietyId($query, $society_id)
    {
        if ($society_id) {
            return $query->where('society_id', '=', $society_id);
        }
        return $query;
    }

    public function scopeSearchByCategory($query, $complaint_category_id)
    {
        if ($complaint_category_id) {
            return $query->where('complaint_category_id', '=', $complaint_category_id);
        }
        return $query;
    }

    public function getCardFileAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

}
