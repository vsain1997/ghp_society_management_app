<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'rating',
        'feedback',
        'feedback_by',
        'society_id',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
