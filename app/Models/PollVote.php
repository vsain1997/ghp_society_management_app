<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollVote extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',//voter persion
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function option()
    {
        return $this->belongsTo(PollOption::class);
    }

    public function voters()
    {
        return $this->belongsTo(User::class);
    }
}
