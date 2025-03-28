<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'visibility',
        'end_date',
        'society_id',
        'created_by'
    ];

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSearchByVisibility($query, $visibility)
    {
        if ($visibility) {
            return $query->where('visibility', '=', $visibility);
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
