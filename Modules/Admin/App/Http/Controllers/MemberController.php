<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Society;
use App\Models\Block;
use App\Models\Member;
use App\Models\MemberDailyHelpStaff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:member.view|member.create|member.edit|member.delete|member.status_change')->only(['index', 'show']);
        $this->middleware('permission:member.create')->only(['store']);
        $this->middleware('permission:member.edit')->only(['edit', 'update']);
        $this->middleware('permission:member.delete')->only(['destroy']);
        $this->middleware('permission:member.status_change')->only(['changeStatus']);
    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Member Index Accessed', description: 'Accessing member index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            $members = Member::with('block')
                ->searchByStatus($status)
                ->when($search && $search_col, function ($query) use ($search, $search_col) {
                    // Apply dynamic column search
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->where('society_id', $selectedSociety)
                ->orderBy('name', 'asc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Members Retrieved', description: 'Members retrieved', status: 'success', severityLevel: 1);

            return view(
                'admin::member.member',
                [
                    'members' => $members,
                    'search' => $search,
                ]
            );
        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Member Index Error', description: 'Exception during member index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    // Store a newly created member
    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Member Creation Started', description: 'Starting the process of creating a new member');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'role' => 'required',
                'phone' => 'required|string',
                'email' => 'required|email',
                'society_id' => 'required|integer',
                'aprt_no' => 'required|integer',
                'ownership' => 'required|string',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Member Creation Validation Failed', description: 'Validation error during member creation: ' . $validator->errors()->first(), modelType: 'Member', modelId: null, status: 'failed');

                return redirect()->back()
                    ->withInput()
                    ->with([
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ]);
            }

            // check is vacancy left in selected location
            // $getTotalOccupied = Member::where('society_id', $request->input('society_id'))
            //     ->where('status', 'active')
            //     ->where('deleted_at', NULL)
            //     ->where('block_id', $request->input('block_id'))
            //     // ->where('floor_number', $request->input('floor_number'))
            //     ->where('unit_type', $request->input('unit_type'))
            //     // ->where('aprt_no', $request->input('aprt_no'))
            //     ->count();

            // $getBlockUnitInfo = Block::where('id', $request->input('block_id'))
            //     ->first();
            // $totalUnit = $getBlockUnitInfo->total_units;
            // $leftVacantUnit = $totalUnit - $getTotalOccupied;

            // if ($leftVacantUnit == 0) {
            //     return redirect()->back()
            //         ->withInput()
            //         ->with([
            //             'status' => 'error',
            //             'message' => 'No unit available !',
            //         ]);
            // }

            // create user
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = $request->input('role');
            $user->status = 'active';
            // $user->password = Hash::make(trim($request->input('password')));
            $user->password = bcrypt(trim($request->input('password')));
            $user->save();

            //get block info
            $blockInfo = Block::find($request->input('aprt_no'));//dont be confuse , it is block id

            // create member
            $member = new Member();
            $member->name = $request->input('name');
            $member->role = $request->input('role');
            $member->phone = $request->input('phone');
            $member->email = $request->input('email');
            $member->society_id = $request->input('society_id');
            $member->block_id = $blockInfo->id;
            $member->floor_number = $blockInfo->floor;
            $member->unit_type = $blockInfo->unit_type;
            $member->aprt_no = $blockInfo->property_number;
            $member->user_id = $user->id;
            $member->ownership_type = $request->input('ownership');
            $member->owner_name = $request->input('owner_name');
            $member->emer_name = $request->input('emer_name');
            $member->emer_relation = $request->input('emer_relation');
            $member->emer_phone = $request->input('emer_phone');
            $member->save();

            // save notification defaults values
            $insertDefaultNotification = [];
            $residentNotifications = config('notification_settings.resident_app');
            foreach ($residentNotifications as $defaultSettingName) {
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

            _dLog(eventType: 'info', activityName: 'Member Created', description: 'New member created', modelType: 'Member', modelId: $member->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $member->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Added successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Member Creation Failed', description: 'Exception during member creation: ' . $e->getMessage(), modelType: 'Member', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function edit($id)
    {
        try {

            $member = Member::with('block')->find($id);

            if (!$member) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found'
                ]);
            }
            return response()->json([
                'status' => 'success',
                'data' => $member
            ]);

        } catch (Exception $e) {
            // 'error' => $e->getMessage(),
            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    // Update a member
    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Member Update Started', description: 'Starting the process of update member', modelType: 'Member', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'role' => 'required',
                'phone' => 'required|string',
                'email' => 'required|email',
                'society_id' => 'required|integer',
                'aprt_no' => 'required|integer',
                'ownership' => 'required',
                'user_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Validation failed' . $validator->errors(),
                    ]
                );
            }


            $user = User::find($request->input('user_id'));
            if (!$user) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Not found',
                    ]
                );
            }

            if ($user) {
                // Update the user using mass assignment
                $user->update($request->only([
                    'name',
                    'email',
                    'phone',
                    'role',
                ]));
            }

            $member = Member::find($id);
            if (!$member) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Not found',
                    ]
                );
            }
            $member->fill($request->only([
                'name',
                'role',
                'phone',
                'email',
                'society_id',
                // 'block_id',
                // 'aprt_no',
                'user_id'
            ]));
            $blockInfo = Block::find($request->input('aprt_no'));
            $member->block_id = $blockInfo->id;
            $member->floor_number = $blockInfo->floor;
            $member->unit_type = $blockInfo->unit_type;
            $member->aprt_no = $blockInfo->property_number;
            $member->ownership_type = $request->input('ownership');
            $member->owner_name = $request->input('owner_name');
            $member->emer_name = $request->input('emer_name');
            $member->emer_relation = $request->input('emer_relation');
            $member->emer_phone = $request->input('emer_phone');
            $member->save();

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Member Updated', description: 'Member updated ( Title : ' . $member->name . ' ) ', modelType: 'Member', modelId: $member->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $member->toArray(), requestData: $request->all());

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Updated successfully',
                ]
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Member Update Failed', description: 'Exception during member update: ' . $e->getMessage(), modelType: 'Member', modelId: $id, status: 'failed', severityLevel: 2);

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $member = Member::with('block', 'society')->find($id);

            if (!$member) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            $dailyHelpStaffs = MemberDailyHelpStaff::with('staff')->where('member_user_id', $member->user_id)->get();

            _dLog(eventType: 'info', activityName: 'Member Details Accessed', description: 'Accessing details of member ', modelType: 'Member', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::member.details',
                compact('member', 'dailyHelpStaffs')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Member Details Error', description: 'Exception during member details retrieval: ' . $e->getMessage(), modelType: 'Member', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    // Delete a member
    public function destroy($id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Member Deletion Started', description: 'Starting the process of deleting ', modelType: 'Member', modelId: $id);

            DB::beginTransaction();
            $member = Member::find($id);
            if (!$member) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found'
                ]);
            }
            $member->delete();
            $member->user()->delete();
            // $member->forceDelete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Member Deleted', description: 'Member deleted ( Title : ' . $member->name . ' ) ', modelType: 'Member', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            _dLog(eventType: 'error', activityName: 'Member Deletion Failed', description: 'Exception during member deletion: ' . $e->getMessage(), modelType: 'Member', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again'
            ]);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Member Status Change Started', description: 'Changing status for member to ' . $status);

            $member = Member::find($id);
            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $member->status = $status;
            $member->save();

            $user = User::find($member->user_id);
            $user->status = $status;
            $user->save();

            _dLog(eventType: 'info', activityName: 'Member Status Changed', description: 'Member status updated ( Title : ' . $member->name . ' ) ', modelType: 'Member', modelId: $member->id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Member Status Change Failed', description: 'Exception during member status change: ' . $e->getMessage(), modelType: 'Member', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }
}
