<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sos extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'sos_category_id',
        'alert_by',
        'society_id',
        'block_id',
        'area',
        'description',
        'phone',
        'floor',
        'unit_no',
        'unit_type',
        'date',
        'time',
        'status',
    ];

    public function sosCategory()
    {
        return $this->belongsTo(related: SosCategory::class, foreignKey: 'sos_category_id', ownerKey: 'id');
    }

    // Relation to User
    public function user()
    {
        return $this->belongsTo(User::class, 'alert_by');
    }

    // Relation to Society
    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    // Relation to Block
    public function block()
    {
        return $this->belongsTo(Block::class);
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

    public function scopeSearchByResident($query, $created_by)
    {
        if ($created_by) {
            return $query->where('alert_by', '=', $created_by);
        }
        return $query;
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');//->withDefault();
    }

}
