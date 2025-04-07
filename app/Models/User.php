<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'status',
        'name',
        'email',
        'password',
        'image',
        'phone',
        'device_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_verified_at',
        'otp_expire_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_verified_at' => 'datetime',
    ];

    protected $appends = ['member_id', 'society_id', 'staff_id', 'image_url', 'last_checkin_detail'];

    public function getMemberIdAttribute()
    {
        $member = $this->member;
        return $member ? $member->id : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? url('storage/' . $this->image) : null;
    }

    public function getStaffIdAttribute()
    {
        $staff = $this->staff;
        return $staff ? $staff->id : null;
    }

    public function getSocietyIdAttribute()
    {
        if ($this->role == 'resident' || $this->role == 'admin') {

            $member = $this->member;
            return $member ? $member->society_id : null;
        }
        //  elseif ($this->role == 'service_provider') {
        //     $member = $this->serviceProvider;
        //     return $member ? $member->society_id : null;
        // }
        elseif (
            $this->role == 'staff' || $this->role == 'staff_security_guard'
        ) {
            $staff = $this->staff;
            return $staff ? $staff->society_id : null;
        }
    }

    public static function boot()
    {
        parent::boot();
        static::creating(fn($u) => $u->uid = Uuid::uuid4());
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function noticesCreated()
    {
        return $this->hasMany(Notice::class, 'created_by');
    }

    public function eventsCreated()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function documentsUploadedBy()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }
    public function documentsRequestedBy()
    {
        return $this->hasMany(Document::class, 'request_by');
    }

    public function pollsCreated()
    {
        return $this->hasMany(Poll::class, 'created_by');
    }

    public function pollsVoted()
    {
        return $this->hasMany(PollVote::class, 'user_id');
    }

    public function referedProperties()
    {
        return $this->hasMany(ReferProperty::class, 'user_id');
    }

    public function myBills()
    {
        return $this->hasMany(Bill::class, 'user_id');
    }

    public function myUnpaidBills()
    {
        return $this->hasMany(Bill::class, 'user_id')->where('status', 'unpaid');
    }

    public function myCurrentMonthBills()
    {
        return $this->hasMany(Bill::class, 'user_id')
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    public function myGeneratedBills()
    {
        return $this->hasMany(Bill::class, 'created_by');
    }

    public function TradeProperty()
    {
        return $this->hasMany(TradeProperty::class, 'created_by');
    }

    public function mySosAlerts()
    {
        return $this->hasMany(Sos::class, 'alert_by');
    }

    public function serviceProvider()
    {
        return $this->hasOne(ServiceProviders::class);
    }

    public function complaintBy()
    {
        return $this->hasMany(Complaint::class, 'complaint_by');
    }

    public function complaintAssignedTo()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    public function residentCallbackRequests()
    {
        return $this->hasMany(CallbackRequest::class, 'request_by');
    }

    public function callbackRequestToServiceProviders()
    {
        return $this->hasMany(CallbackRequest::class, 'request_to');
    }

    public function staff()
    {
        return $this->hasOne(related: Staff::class);
    }

    public function activityLog()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * override start ==================================================
     * By default, Spatie Laravel Permission checks both role_has_permissions and model_has_permissions.
     * Now , here  hasPermissionTo and can is orveride for the permissions get from model_has_permissions table only.
     */

    public function getAllPermissions()
    {
        // Fetch permissions from model_has_permissions table
        $permissionIds = DB::table('model_has_permissions')
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->pluck('permission_id');

        // Fetch the actual permission details from the permissions table
        return Permission::whereIn('id', $permissionIds)->get();
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permissionModel = Permission::where('name', $permission)->first();

        // If permission doesn't exist, return false
        if (!$permissionModel) {
            return false;
        }

        // Check only `model_has_permissions` table
        return DB::table('model_has_permissions')
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->where('permission_id', $permissionModel->id)
            ->exists();
    }

    public function can($abilities, $arguments = []): bool
    {
        // Handle multiple permissions (e.g., "permission1|permission2")
        $permissions = is_string($abilities) ? explode('|', $abilities) : (array) $abilities;

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }
    /**
     * override end ./=================================================
     */

    public function notificationSettings()
    {
        return $this->hasMany(NotificationSettings::class);
    }

    public function getDeviceTokens()
    {
        // Assuming you have a device_tokens column or a related table to store tokens
        return $this->device_ids ?? [];//device_id for only one device logged in.
    }

    public function routeNotificationForFcm()
    {
        return $this->getDeviceTokens();
    }

    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class, 'model_has_permissions', 'model_id', 'permission_id');
    // }


    // public function getLastCheckinDetailAttribute()
    // {
    //     // return CheckinDetail::where('by_resident', $this->id)
    //     //     ->orderBy('checkin_at', 'desc')
    //     //     ->first();

    //     return CheckinDetail::where(function ($query) {
    //         if (!empty($this->by_resident)) {
    //             $query->where('by_resident', $this->id);
    //         } elseif (!empty($this->by_daily_help)) {
    //             $query->where('by_daily_help', $this->id);
    //         }
    //     })
    //         ->orderBy('checkin_at', 'desc')
    //         ->first();
    // }

    public function getLastCheckinDetailAttribute()
    {
        $checkinDetail = CheckinDetail::with([
            'checkedInBy',
            'checkedOutBy'
        ])->where(function ($query) {
            $query->where('by_resident', $this->id)
                ->orWhere('by_daily_help', $this->id);
        })
            ->whereIn('status', ['checked_in', 'checked_out']) //only guard check-in-out related
            ->orderBy('checkin_at', 'desc')
            ->first() ?: null; // Ensures null when no match is found
        if ($checkinDetail) {
            // Hide fields from the main object
            $checkinDetail->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);

            // Hide fields from relationships
            if ($checkinDetail->checkedInBy) {
                $checkinDetail->checkedInBy->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
            }

            if ($checkinDetail->checkedOutBy) {
                $checkinDetail->checkedOutBy->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
            }
        }

        return $checkinDetail;
    }



    // Fetch staff assigned to this user (resident/admin)
    public function assignedDailyHelpStaffs()
    {
        return $this->hasMany(MemberDailyHelpStaff::class, 'member_user_id');
    }

    // Fetch members assigned to this staff (for guard/admin view)
    public function assignedDailyHelpMembers()
    {
        return $this->hasMany(MemberDailyHelpStaff::class, 'staff_user_id');
    }


}
