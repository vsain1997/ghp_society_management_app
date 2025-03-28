<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ComplaintFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'complaint_id',
        'path',
        'file_type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function getPathAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }

}
