<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'name',
        'role',
        'phone',
        'email',
        'society_id',
        'block_id',
        'floor_number',
        'unit_type',
        'aprt_no',
        'maintenance_bill',
        'maintenance_bill_due_date',
        'user_id',
        'ownership_type',
        'owner_name',
        'emer_name',
        'emer_relation',
        'emer_phone',
        
        
    ];
    public $timestamps = false;

    protected $dates = ['deleted_at'];//treat as date

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function society()
    {
        return $this->belongsTo(Society::class);
    }
    public function admin_of_society()
    {
        return $this->hasOne(related: Society::class, foreignKey: 'member_id', localKey: 'user_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
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

    public function visitingResidents()
    {
        return $this->hasMany(Visitor::class, 'user_id', 'user_id');
    }

}
