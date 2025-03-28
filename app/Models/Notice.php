<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'date',
        'time',
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
}
