<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorBulk extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'name',
        'phone',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

}
