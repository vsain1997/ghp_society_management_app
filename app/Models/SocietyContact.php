<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SocietyContact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'phone',
        'society_id',
    ];
    protected $dates = ['deleted_at'];//treat as date

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
