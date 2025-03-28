<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberDailyHelpStaff extends Model
{
    use HasFactory;

    protected $table = 'member_daily_help_staffs';

    protected $fillable = [
        'member_user_id',
        'staff_user_id',
        'society_id',
        'shift_from',
        'shift_to'
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    // Get the user who is assigned this staff (Resident/Admin)
    // public function memberUser()
    // {
    //     return $this->belongsTo(User::class, 'member_user_id')->without(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
    // }
    public function memberUser()
    {
        return $this->belongsTo(User::class, 'member_user_id')
            // ->withDefault()->tap(function ($query) {
            //     $query->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
            // })
        ;
    }


    // Get the members assigned to a staff
    public function member()
    {
        return $this->hasOne(Member::class, 'user_id', 'member_user_id');
    }


}
