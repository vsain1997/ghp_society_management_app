<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\CheckinDetail;
use App\Models\Member;
use App\Models\MemberDailyHelpStaff;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Society;
use App\Models\Block;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StaffController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:staff.view|staff.create|staff.edit|staff.delete|staff.change_status')->only(['index', 'show']);
        $this->middleware('permission:staff.create')->only(['store']);
        $this->middleware('permission:staff.edit')->only(['edit', 'update']);
        $this->middleware('permission:staff.delete')->only(['destroy']);
        $this->middleware('permission:staff.change_status')->only(['changeStatus']);
    }


    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Staff Index Accessed', description: 'Accessing staff index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'name');

            $staffs = Staff::with('society')
                ->searchByStatus($status)
                ->when($search && $search_col, function ($query) use ($search, $search_col) {
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->where('society_id', $selectedSociety)
                ->orderBy('id', 'desc')
                ->paginate(25);

            $complaint_categories = ComplaintCategory::all();

            $staffRoles = DB::select("SELECT name, label FROM staff_roles");

            //get all society residents
            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $selectedSociety)
                ->get();

            _dLog(eventType: 'info', activityName: 'Staffs Retrieved', description: 'Staffs retrieved', status: 'success', severityLevel: 1);

            return view('admin::staff.staff', [
                'staffs' => $staffs,
                'complaint_categories' => $complaint_categories,
                'search' => $search,
                'staffRoles' => $staffRoles,
                'societyResidents' => $societyResidents,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Staff Index Error', description: 'Exception during staff index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Staff Creation Started', description: 'Starting the process of creating a new staff');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'role' => 'required|string',
                'complaint_category_id' => 'nullable|integer',
                'employee_id' => 'required|string',
                'gender' => 'required|string',
                'dob' => 'required',
                'phone' => 'required|string',
                'email' => 'required|email',
                'location' => 'required|string',
                'card_type' => 'required|string',
                'card_number' => 'required|string',
                'card_file' => 'required|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Staff Creation Validation Failed', description: 'Validation error during staff creation: ' . $validator->errors()->first(), modelType: 'Staff', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            $shift_from = null;
            $shift_to = null;
            $off_days = null;
            if ($request->filled('shift_from')) {
                $shift_from = Carbon::createFromFormat('h:i A', $request->input('shift_from'), 'Asia/Kolkata')->format('H:i:s');
            }

            if ($request->filled('shift_to')) {
                $shift_to = Carbon::createFromFormat('h:i A', $request->input('shift_to'), 'Asia/Kolkata')->format('H:i:s');
            }

            if ($request->filled('off_days')) {
                $off_days = implode(',', $request->input('off_days'));
            }

            if ($request->filled('role') == 'other' && $request->filled('other_role_name')) {

                $role = 'staff_' . strtolower(str_replace(' ', '_', $request->input('other_role_name')));
                $label = $request->input('other_role_name');

                DB::insert("INSERT INTO staff_roles (name, label, created_at, updated_at) VALUES (?, ?, ?, ?)", [
                    $role,
                    $label,
                    now(),
                    now()
                ]);

            } else {
                $role = $request->input('role');
            }

            // user
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = $role;//$request->input('role');
            $user->status = 'active';
            $user->password = '';//Hash::make($request->input('password'));
            $user->save();

            // staff
            $staff = new Staff();
            $staff->name = $request->input('name');
            $staff->daily_help = $request->has('daily_help') ? 1 : 0; // Default to 0 if unchecked
            $staff->role = $role;
            $staff->employee_id = $request->input('employee_id');
            $staff->assigned_area = $request->input('assigned_area');
            $staff->gender = $request->input('gender');
            $staff->dob = $request->input('dob');
            $staff->phone = $request->input('phone');
            $staff->email = $request->input('email');
            $staff->address = $request->input('location');
            $staff->shift_from = $shift_from;
            $staff->shift_to = $shift_to;
            $staff->off_days = $off_days;

            $staff->emer_name = $request->input('emer_name');
            $staff->emer_relation = $request->input('emer_relation');
            $staff->emer_phone = $request->input('emer_phone');

            $staff->date_of_join = $request->input('date_of_join');
            $staff->contract_end_date = $request->input('contract_end_date');
            $staff->monthly_salary = is_numeric($request->input('monthly_salary'))
                ? number_format((float) $request->input('monthly_salary'), 2, '.', '')
                : '0.00';

            $staff->card_type = $request->input('card_type');
            $staff->card_number = $request->input('card_number');

            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            $path = $request->file('card_file')->store('staffs', 'public');
            $staff->card_file = $path;
            $staff->status = 'active';
            $staff->society_id = $request->input('society_id');
            if ($request->filled('complaint_category_id')) {
                // Perform action if complaint_category_id is not empty
                $staff->complaint_category_id = $request->input('complaint_category_id');
            }
            $staff->user_id = $user->id;
            $staff->save();

            // save notification defaults values
            $insertDefaultNotification = [];
            if ($user->role == 'staff_security_guard') {

                $defNotifications = config('notification_settings.security_guard_app');
            } elseif ($user->role == 'staff') {

                $defNotifications = config('notification_settings.service_provider_app');
            } else {
                $defNotifications = config('notification_settings.default_staff_app');
            }
            foreach ($defNotifications as $defaultSettingName) {
                $insertDefaultNotification[] = [
                    'name' => $defaultSettingName,
                    'status' => 'enabled',
                    'user_of_system' => 'app',
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'society_id' => $request->input('society_id')
                ];
            }
            if (!empty($insertDefaultNotification)) {
                DB::table('notification_settings')->insert($insertDefaultNotification);
            }

            _dLog(eventType: 'info', activityName: 'Staff Created', description: 'New staff created', modelType: 'Staff', modelId: $staff->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $staff->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Staff added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Staff Creation Failed', description: 'Exception during staff creation: ' . $e->getMessage(), modelType: 'Staff', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $staff = Staff::with('staffCategory', 'society')->find($id);

            if (!$staff) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            $dailyHelpStaffs = MemberDailyHelpStaff::with('memberUser')->where('staff_user_id', $staff->user_id)->get();

            $checkin_logs = CheckinDetail::with([
                'dailyHelpMemberDetails' => function ($query) {
                    $query->select('users.*')->with([
                        'member' => function ($query) {
                            $query->select(
                                'members.id',
                                'members.user_id',
                                'members.name',
                                'members.aprt_no',
                                'members.floor_number',
                                'members.unit_type',
                                'members.phone',
                                'blocks.name as block_name'
                            )->join('blocks', 'members.block_id', '=', 'blocks.id');
                        }
                    ]);
                },
                'checkedInBy',
                'checkedOutBy'
            ])->where('by_daily_help', $staff->user_id);


            $checkin_logs = $checkin_logs->orderBy('checkin_at', 'desc')
                ->paginate(10);

            $checkin_logs->each(function ($log) {
                if ($log->dailyHelpMemberDetails) {
                    // $log->dailyHelpMemberDetails->setAppends([]); // Remove all appended attributes
                    $log->dailyHelpMemberDetails->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']); // Ensure it's hidden
                }
                if ($log->checkedInBy) {
                    $log->checkedInBy->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
                }

                if ($log->checkedOutBy) {
                    $log->checkedOutBy->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
                }
            });

            _dLog(eventType: 'info', activityName: 'Staff Details Accessed', description: 'Accessing details of staff ', modelType: 'Staff', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::staff.details',
                compact('staff', 'dailyHelpStaffs', 'checkin_logs')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Staff Details Error', description: 'Exception during staff details retrieval: ' . $e->getMessage(), modelType: 'Staff', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $staff = Staff::find($id);

            if (!$staff) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Staff Edit Accessed', description: 'Accessing edit page for staff ', modelType: 'Staff', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $staff,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Staff Edit Error', description: 'Exception during staff edit retrieval: ' . $e->getMessage(), modelType: 'Staff', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Staff Update Started', description: 'Starting the process of update staff', modelType: 'Staff', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'role' => 'required|string',
                'complaint_category_id' => 'nullable|integer',
                'phone' => 'required|string',
                'email' => 'required|email',
                'location' => 'required|string',
                'card_type' => 'required|string',
                'card_number' => 'required|string',
                'employee_id' => 'required|string',
                'gender' => 'required|string',
                'dob' => 'required'
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Staff Update Validation Failed', description: 'Validation error during staff update', modelType: 'Staff', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $staff = Staff::find($id);
            if (!$staff) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            $user = User::find($staff->user_id);


            $shift_from = null;
            $shift_to = null;
            $off_days = null;
            if ($request->filled('shift_from')) {
                $shift_from = Carbon::createFromFormat('h:i A', $request->input('shift_from'), 'Asia/Kolkata')->format('H:i:s');
            }

            if ($request->filled('shift_to')) {
                $shift_to = Carbon::createFromFormat('h:i A', $request->input('shift_to'), 'Asia/Kolkata')->format('H:i:s');
            }

            if ($request->filled('off_days')) {
                $off_days = implode(',', $request->input('off_days'));
            }

            if ($request->filled('role') == 'other' && $request->filled('other_role_name')) {

                $role = 'staff_' . strtolower(str_replace(' ', '_', $request->input('other_role_name')));
                $label = $request->input('other_role_name');

                DB::insert("INSERT INTO staff_roles (name, label, created_at, updated_at) VALUES (?, ?, ?, ?)", [
                    $role,
                    $label,
                    now(),
                    now()
                ]);

            } else {
                $role = $request->input('role');
            }
            // =============================================
            if ($user) {
                $previousRole = $user->role;
                $changeRequestRole = $role;
                $roleToNotifications = [
                    'staff_security_guard' => config('notification_settings.security_guard_app'),
                    'staff' => config('notification_settings.service_provider_app'),
                    'default' => config('notification_settings.default_staff_app'),
                    // Add more roles and their respective notification settings here
                ];

                // Check if the role change exists in the mapping
                if ($previousRole !== $changeRequestRole) {
                    // Delete old notification settings
                    DB::table('notification_settings')
                        ->where('user_id', $user->id)
                        ->delete();

                    // Determine the notification settings to use based on the new role
                    $defNotifications = $roleToNotifications[$changeRequestRole] ?? $roleToNotifications['default'];
                    // Prepare the data for insertion
                    $insertDefaultNotification = [];
                    foreach ($defNotifications as $defaultSettingName) {
                        $insertDefaultNotification[] = [
                            'name' => $defaultSettingName,
                            'status' => 'enabled',
                            'user_of_system' => 'app',
                            'user_id' => $user->id,
                            'role' => $changeRequestRole,
                            'society_id' => $request->input('society_id')
                        ];
                    }

                    // Insert the new notification settings
                    if (!empty($insertDefaultNotification)) {
                        DB::table('notification_settings')->insert($insertDefaultNotification);
                    }
                }
            }
            // =============================================

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = $role;
            // $user->status = 'active';
            // $user->password = '';//Hash::make($request->input('password'));
            $user->save();

            // staff
            $staff->name = $request->input('name');
            $staff->daily_help = $request->has('daily_help') ? 1 : 0; // Default to 0 if unchecked
            $staff->role = $role;
            $staff->phone = $request->input('phone');
            $staff->email = $request->input('email');
            $staff->address = $request->input('location');

            $staff->employee_id = $request->input('employee_id');
            $staff->assigned_area = $request->input('assigned_area');
            $staff->gender = $request->input('gender');
            $staff->dob = $request->input('dob');
            $staff->shift_from = $shift_from;
            $staff->shift_to = $shift_to;
            $staff->off_days = $off_days;

            $staff->emer_name = $request->input('emer_name');
            $staff->emer_relation = $request->input('emer_relation');
            $staff->emer_phone = $request->input('emer_phone');

            $staff->date_of_join = $request->input('date_of_join');
            $staff->contract_end_date = $request->input('contract_end_date');
            $staff->monthly_salary = is_numeric($request->input('monthly_salary'))
                ? number_format((float) $request->input('monthly_salary'), 2, '.', '')
                : '0.00';

            $staff->card_type = $request->input('card_type');
            $staff->card_number = $request->input('card_number');

            if ($request->hasFile('card_file') && $request->file('card_file')->isValid()) {
                ini_set('upload_max_filesize', '10M');
                ini_set('post_max_size', '10M');
                $path = $request->file('card_file')->store('staffs', 'public');
                $staff->card_file = $path;
            }

            $staff->society_id = $request->input('society_id');
            if ($request->filled('complaint_category_id')) {
                // Perform action if complaint_category_id is not empty
                $staff->complaint_category_id = $request->input('complaint_category_id');
            }
            // $staff->user_id = $user->id;
            $staff->save();

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Staff Updated', description: 'Staff updated ( Name : ' . $staff->name . ' ) ', modelType: 'Staff', modelId: $staff->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $staff->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Staff updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Staff Update Failed', description: 'Exception during staff update: ' . $e->getMessage(), modelType: 'Staff', modelId: $id, status: 'failed', severityLevel: 2);

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Staff Deletion Started', description: 'Starting the process of deleting', modelType: 'Staff', modelId: $id);

            DB::beginTransaction();

            $staff = Staff::find($id);

            if (!$staff) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Staff not found',
                ]);
            }

            $user = User::find($staff->user_id);

            // Delete the staff record first to avoid foreign key constraint violation
            $staff->delete();

            // Delete the associated user record
            if ($user) {
                $user->delete();
            }

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Staff Deleted', description: 'Staff deleted (Name: ' . $staff->name . ')', modelType: 'Staff', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Staff Deletion Failed', description: 'Exception during staff deletion: ' . $e->getMessage(), modelType: 'Staff', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }


    public function changeStatus($id, $status)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Staff Status Change Started', description: 'Changing status for staff to ' . $status);

            $staff = Staff::find($id);
            if (!$staff) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }

            $staff->status = $status;
            $staff->save();

            $user = User::find($staff->user_id);
            $user->status = $status;
            $user->save();

            _dLog(eventType: 'info', activityName: 'Staff Status Changed', description: 'Staff status updated ( Name : ' . $staff->name . ' ) ', modelType: 'Staff', modelId: $staff->id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Staff Status Change Failed', description: 'Exception during staff status change: ' . $e->getMessage(), modelType: 'Staff', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function assignMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_user_id' => 'required|integer',
            'user_id' => 'nullable|array', // Array of member IDs
            'm_shift_from' => 'nullable|array',
            'm_shift_to' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            _dLog(eventType: 'error', activityName: 'Assign Member To Daily Help Staff Validation Failed', description: 'Validation error during Assign Member To Daily Help Staff: ' . $validator->errors()->first(), modelType: 'MemberDailyHelpStaff', modelId: null, status: 'failed');

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            DB::beginTransaction();

            $staffUserId = $request->staff_user_id;
            $staffDetail = Staff::select('society_id')->where('user_id', $staffUserId)->first();
            $postedMemberIds = $request->user_id ?? []; // **Ensure an empty array if null**
            $shiftFromTimes = $request->m_shift_from ?? [];
            $shiftToTimes = $request->m_shift_to ?? [];

            // Fetch existing members assigned to this staff
            $existingMemberIds = MemberDailyHelpStaff::where('staff_user_id', $staffUserId)
                ->pluck('member_user_id')
                ->toArray();
            // **Delete all assigned members if no `user_id` is provided**
            if (empty($postedMemberIds)) {
                MemberDailyHelpStaff::where('staff_user_id', $staffUserId)->delete();
            } else {
                // Insert or Update members
                foreach ($postedMemberIds as $index => $memberId) {
                    MemberDailyHelpStaff::updateOrCreate(
                        [
                            'member_user_id' => $memberId,
                            'staff_user_id' => $staffUserId
                        ],
                        [
                            'society_id' => $staffDetail->society_id, // Assuming user has a society_id
                            'shift_from' => $shiftFromTimes[$index] ?? null,
                            'shift_to' => $shiftToTimes[$index] ?? null
                        ]
                    );
                }

                // Delete members not in postedMemberIds
                $membersToDelete = array_diff($existingMemberIds, $postedMemberIds);
                if (!empty($membersToDelete)) {
                    MemberDailyHelpStaff::where('staff_user_id', $staffUserId)
                        ->whereIn('member_user_id', $membersToDelete)
                        ->delete();
                }
            }

            _dLog(eventType: 'info', activityName: 'Assign Member To Daily Help Staff', description: 'Assign Member To Daily Help Staff', modelType: 'MemberDailyHelpStaff', modelId: 0, status: 'success', severityLevel: 1, beforeData: null, afterData: null, requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Assigned successfull',
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Assign Member To Daily Help Staff Failed', description: 'Exception during assign member to daily help dtaff: ' . $e->getMessage(), modelType: 'MemberDailyHelpStaff', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function getAssignedMembers($staffUserId)
    {
        try {
            $assignedMembers = DB::table('member_daily_help_staffs')
                ->where('staff_user_id', $staffUserId)
                ->join('users', 'member_daily_help_staffs.member_user_id', '=', 'users.id')
                ->select('member_daily_help_staffs.*', 'users.name')
                ->get();

            $assignedMemberIds = $assignedMembers->pluck('member_user_id')->toArray();


            //get all society residents
            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', session('__selected_society__'))
                ->whereNotIn('members.user_id', $assignedMemberIds) // Exclude assigned members
                ->get();


            return response()->json(['success' => true, 'data' => $assignedMembers, 'unassigned_residents' => $societyResidents]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

}
