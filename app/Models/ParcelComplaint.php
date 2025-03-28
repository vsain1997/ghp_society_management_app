<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParcelComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'date',
        'time',
        'description',
        'complain_of',
        'society_id'
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'parcel_id');
    }

    public function complainantOf()
    {
        return $this->belongsTo(User::class, 'complain_of');
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
