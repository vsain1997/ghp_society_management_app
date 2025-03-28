<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Complaint extends Model
{
    use HasFactory;
    protected $fillable = [
        'complaint_category_id',
        'complaint_by',
        'society_id',
        'block_id',
        'block_name',
        'unit_type',
        'floor_number',
        'aprt_no',
        'area',
        'description',
        'otp',
        'status',
        'assigned_to',
        'assigned_by',
        'complaint_at',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ComplaintCategory::class, 'complaint_category_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'complaint_category_id', 'complaint_category_id');
    }

    public function complaintFiles()
    {
        return $this->hasMany(ComplaintFile::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function complaintBy()
    {
        return $this->belongsTo(User::class, 'complaint_by');
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

    public function scopeSearchByResident($query, $complaint_by)
    {
        if ($complaint_by) {
            return $query->where('complaint_by', '=', $complaint_by);
        }
        return $query;
    }

    public function scopeSearchByCategoryId($query, $category_id)
    {
        if ($category_id) {
            return $query->where('complaint_category_id', '=', $category_id);
        }
        return $query;
    }

    public function scopeSearchByAssignedTo($query, $assigned_to)
    {
        if ($assigned_to) {
            return $query->where('assigned_to', '=', $assigned_to);
        }
        return $query;
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'complaint_by', 'user_id');
    }

}
