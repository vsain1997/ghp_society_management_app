<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\MembersImport;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // get society
        // $selectedSociety = session('__selected_society__');
        // if (!$selectedSociety) {
        //     $selectedSociety = Society::orderBy('id', 'asc')->first();
        //     if (!$selectedSociety) {
        //         //when no society created
        //         session(['active_tab' => '#tab3-tab']);//society tab
        //         return redirect()->route('superadmin.settings')->with([
        //             'status' => 'warning',
        //             'message' => 'Please create a Society First !'
        //         ]);
        //     }
        //     session(['__selected_society__' => $request->society_id]);
        // }
        $selectedSociety = getSelectedSociety($request);
        $blocks = Block::where('society_id', $selectedSociety)
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get();
        if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
            return $selectedSociety; // Redirect if necessary
        }

        // Continue with business logic using $selectedSociety


        // $search = $request->input('search', '');
        // $search_col = $request->input('search_for', '');
        // // Fetch societies with filters and pagination
        // $members = Member::with('block')
        //     ->when($search, function ($query) use ($search) {
        //         return $query->where(function ($query) use ($search) {
        //             $query->where('name', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('phone', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('email', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('status', 'LIKE', '%' . $search . '%');
        //         });
        //     })
        //     ->where('society_id', $selectedSociety)
        //     ->orderBy('id', 'desc')
        //     ->paginate(25);

        $status = $request->input(key: 'status', default: 'active');
        $search = $request->input(key: 'search', default: '');
        $tower = $request->input(key: 'tower', default: '');
        $search_col = $request->input(key: 'search_for', default: '');

        $members = Member::with('block')
            ->searchByStatus($status)
            ->when($search && $search_col, function ($query) use ($search, $search_col) {
                // Apply dynamic column search
                return $query->where($search_col, 'LIKE', '%' . $search . '%');
            })
            ->when($tower,function($query) use ($tower) {
                // Apply tower filter
                return $query->whereHas('block', function ($q) use ($tower) {
                    $q->where('name', 'LIKE', '%' . $tower . '%');
                });
            })
            ->where('society_id', $selectedSociety)
            ->orderBy('id', 'desc')
            ->paginate(25);


        return view(
            'superadmin::member.member',
            [
                'members' => $members,
                'search' => $search,
                'blocks' => $blocks,
            ]
        );
    }

    // Store a newly created member
    public function store(Request $request)
    {
        try {
            superAdminLog('info', 'start::store');
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'role' => 'required',
                'phone' => 'required|string',
                'email' => 'required|email',
                'society_id' => 'required|integer',
                'aprt_no' => 'required|integer',
                'ownership' => 'required|string',
                'maintenance_bill' => 'required'
            ]);

            if ($validator->fails()) {
                superAdminLog('error', 'Validation failed: ' . $validator->errors()->first());
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
            //     ->where('unit_type', $request->input('unit_type'))
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

            superAdminLog('info', 'start::user created');
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
            $member->maintenance_bill = $request->input('maintenance_bill');
            $member->owner_name = $request->input('owner_name');
            $member->emer_name = $request->input('emer_name');
            $member->emer_relation = $request->input('emer_relation');
            $member->emer_phone = $request->input('emer_phone');
            $member->save();
            if ($user->role == 'admin') {

                // Assign the 'admin' role to the user
                $user->assignRole('admin');

                // Fetch all permissions assigned to the 'admin' role
                $adminRole = Role::findByName('admin');
                $adminPermissions = $adminRole->permissions;

                // Assign all permissions of the 'admin' role directly to the user
                $user->syncPermissions($adminPermissions);//seeder is Used
            }

            // =================================================
            // save notification defaults values
            // resident app for resident + admin role
            $insertDefaultNotification = [];
            $residentAppNotifications = config('notification_settings.resident_app');
            foreach ($residentAppNotifications as $defaultSettingName) {
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

            if ($user->role == 'admin') {
                //for admin panel notification settings
                $residentAppNotifications = config('notification_settings.admin_panel');
                $insertDefaultNotificationPanel = [];
                foreach ($residentAppNotifications as $defaultSettingName) {
                    $insertDefaultNotificationPanel[] = [
                        'name' => $defaultSettingName,
                        'status' => 'enabled',
                        'user_of_system' => 'panel',
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'society_id' => $request->input('society_id')
                    ];
                }
                if (!empty($insertDefaultNotificationPanel)) {
                    DB::table('notification_settings')->insert($insertDefaultNotificationPanel);
                }
            }
            // =====================================================

            superAdminLog('info', 'start::member created');
            DB::commit();
            superAdminLog('info', 'end::store');

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Added successfully'
            ]);
        } catch (Exception $e) {
            dd($e);
            superAdminLog('error', 'Exception::', $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }


    //Import file data
     public function importFile(Request $request)
    {
        $societyId = $request->input('society_id');
        $request->validate([
            'importedFile' => 'required|mimes:csv,xlsx,xls|max:5048',
        ]);

        if ($request->hasFile('importedFile')) {
            Excel::import(new MembersImport($societyId), $request->file('importedFile'));
        }

        return back()->with('success', 'Members imported successfully.');
    }

    public function edit($id)
    {
        try {
            // $member = Member::find($id);
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
            superAdminLog('info', 'start::update Member');
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
                'maintenance_bill' => 'required'
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
                $previousRole = $user->role;
                $changeRequestRole = $request->input('role');

                if ($previousRole == 'admin' && $changeRequestRole == 'resident') {
                    //remove user role from 'admin' and remove all permissions
                    // Remove the 'admin' role from the user
                    $user->removeRole('admin');
                    // Revoke all permissions assigned to the user
                    $user->syncPermissions([]);//seeder is Used
                    // =================================================
                    // delete old notification settings
                    DB::table('notification_settings')
                        ->where('user_id', $user->id)
                        ->delete();
                    // save notification defaults values
                    // resident app for resident + admin role
                    $insertDefaultNotification = [];
                    $residentAppNotifications = config('notification_settings.resident_app');
                    foreach ($residentAppNotifications as $defaultSettingName) {
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

                } elseif ($previousRole == 'resident' && $changeRequestRole == 'admin') {
                    // Assign the 'admin' role to the user
                    $user->assignRole('admin');

                    // Fetch all permissions assigned to the 'admin' role
                    $adminRole = Role::findByName('admin');
                    $adminPermissions = $adminRole->permissions;

                    // Assign all permissions of the 'admin' role directly to the user
                    $user->syncPermissions($adminPermissions);//seeder is Used

                    // =================================================
                    // delete old notification settings
                    DB::table('notification_settings')
                        ->where('user_id', $user->id)
                        ->delete();
                    // save notification defaults values
                    // resident app for resident + admin role
                    $insertDefaultNotification = [];
                    $residentAppNotifications = config('notification_settings.resident_app');
                    foreach ($residentAppNotifications as $defaultSettingName) {
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

                    if ($changeRequestRole == 'admin') {
                        //for admin panel notification settings
                        $residentAppNotifications = config('notification_settings.admin_panel');
                        $insertDefaultNotificationPanel = [];
                        foreach ($residentAppNotifications as $defaultSettingName) {
                            $insertDefaultNotificationPanel[] = [
                                'name' => $defaultSettingName,
                                'status' => 'enabled',
                                'user_of_system' => 'panel',
                                'user_id' => $user->id,
                                'role' => $changeRequestRole,
                                'society_id' => $request->input('society_id')
                            ];
                        }
                        if (!empty($insertDefaultNotificationPanel)) {
                            DB::table('notification_settings')->insert($insertDefaultNotificationPanel);
                        }
                    }
                    // =====================================================
                }
                // Update the user using mass assignment
                if ($request->input('role') == 'admin') {

                    if ($request->filled('password')) {

                        $user->update([
                            'password' => bcrypt($request->input('password')),
                            'name' => $request->input('name'),
                            'email' => $request->input('email'),
                            'phone' => $request->input('phone'),
                            'role' => $request->input('role'),
                        ]);

                    } else {

                        $user->update([
                            'name' => $request->input('name'),
                            'email' => $request->input('email'),
                            'phone' => $request->input('phone'),
                            'role' => $request->input('role'),
                        ]);
                    }

                } else {

                    $user->update([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'phone' => $request->input('phone'),
                        'role' => $request->input('role'),
                    ]);
                }
            }

            // if ($user->role == 'admin') {

            //     // Assign the 'admin' role to the user
            //     $user->assignRole('admin');

            //     // Fetch all permissions assigned to the 'admin' role
            //     $adminRole = Role::findByName('admin');
            //     $adminPermissions = $adminRole->permissions;

            //     // Assign all permissions of the 'admin' role directly to the user
            //     $user->syncPermissions($adminPermissions);
            // }

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
            $member->maintenance_bill = $request->input('maintenance_bill');
            $member->save();

            // $member = Member::with('block')->find($member->id);

            DB::commit();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Updated successfully',
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again !' . $e->getMessage()
                //show error message
                // 'error' => $e->getMessage(),
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
            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again'
            ]);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            superAdminLog('info', 'start::changeStatus-member');
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

            superAdminLog('info', 'end<success>::changeStatus-member');
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again!'
            ]);
        }
    }
}
