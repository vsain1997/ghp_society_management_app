<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Society;
use App\Models\Block;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function showLoginForm()
    {
        return view('superadmin::auth.login');
    }

    public function processLogin(Request $request)
    {
        $customMessages = [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'Invalid credentials.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ];

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ], $customMessages);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        $whenRole = 'super_admin';
        $credentials['role'] = $whenRole;

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user();

            if ($user->role == 'super_admin') {

                return redirect()->route('superadmin.dashboard')->with(['status' => 'success', 'message' => 'Welcome ' . $user->name]);
            } else {
                return redirect()->route('superadmin.login.form')->with(['status' => 'error', 'message' => 'Unauthorized access']);
            }
        } else {
            return redirect()->route('superadmin.login.form')->with(['status' => 'error', 'message' => 'Invalid credentials']);
        }
    }

    public function processLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();//ensure session clean
        $request->session()->regenerateToken();//avoid session fixaction attack
        return redirect()->route('superadmin.login.form')->with(['status' => 'success', 'message' => 'Logged out successfully']);
    }


    public function dashboard()
    {
        return view('superadmin::dashboard.dashboard');
    }

    public function profile(Request $request)
    {
        try {
            superAdminLog('info', 'Entering profile method');
            $validator = validator($request->all(), [
                'society_name' => 'sometimes|string|max:255|nullable',
                'society' => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                superAdminLog('warning', 'Validation failed::', $validator->errors()->first());
                // Flash the first error message to the session
                return redirect()->back()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            $user = Auth::user();
            if (!$user) {
                // Log user not found
                superAdminLog('error', 'User not found');

                return redirect()->back()->with([
                    'status' => 'error',
                    'message' => 'User not found!',
                ]);
            }

            $userDetail = User::where('id', $user->id)->first();
            if (!$userDetail) {
                superAdminLog('error', 'User details not found for user ID ' . $user->id);

                return redirect()->back()->with([
                    'status' => 'error',
                    'message' => 'User not found !',
                ]);
            }

            $Filter_societyName = $request->society_name;
            // $Filter_status = $request->status ?? 'active';
            if (!isset($request->society)) {
                $request->society = 'published';
            }
            if ($request->society == 'published') {
                $Filter_status = 'active';
            } else {
                $Filter_status = 'inactive';
            }


            // Fetch societies with filters and pagination
            // $societies = Society::with('blocks')
            //     ->searchByName($Filter_societyName)
            //     ->searchByStatus($Filter_status)
            //     ->orderBy('id', 'desc')
            //     ->paginate(25);



            $societies = Society::with('blocks')
                ->searchByName($Filter_societyName)
                ->searchByStatus($Filter_status)
                ->orderBy('id', 'desc')
                ->paginate(25);

            // // Process each society to calculate total_society_units based on distinct block names
            // $societies->getCollection()->transform(function ($society) {
            //     // Get distinct block names for this society
            //     $distinctBlocks = $society->blocks->unique('name');

            //     // Calculate sum of total_units for distinct block names
            //     $totalSocietyUnits = $distinctBlocks->sum('total_units');

            //     // Add the calculated total_society_units to the society
            //     $society->total_society_units = $totalSocietyUnits;

            //     return $society;
            // });

            $admins = Member::where('status', 'active')
                ->where('role', 'admin')
                ->whereNull('deleted_at')
                ->whereNotExists(function ($query) {
                    $query->select(\DB::raw(1))
                        ->from('societies')
                        ->whereColumn('societies.member_id', 'members.user_id');
                })
                ->get();

            $selectedSocietyId = getSelectedSociety($request);
            $roleOfAdmins = Member::where('status', 'active')
                ->where('role', 'admin')
                ->where('society_id', $selectedSocietyId)
                ->whereNull('deleted_at')
                // ->whereExists(function ($query) {
                //     $query->select(\DB::raw(1))
                //         ->from('societies')
                //         ->whereColumn('societies.member_id', 'members.user_id');
                // })
                ->get();

            $amenities = [
                ["name" => "Gym"],
                ["name" => "Swimming"],
                ["name" => "Clubhouse"],
                ["name" => "Garden"],
                // ["name" => "Walking"],
                ["name" => "Playgound"],
                // ["name" => "Tennis"],
                // ["name" => "Badminton"],
                // ["name" => "Basketball"],
                // ["name" => "Indoor"],
                // ["name" => "Community"],
                ["name" => "Yoga"],
                // ["name" => "CCTV"],
                // ["name" => "Security"],
                // ["name" => "Power"],
                // ["name" => "Intercom"],
                // ["name" => "Lifts"],
                // ["name" => "Rainwater"],
                // ["name" => "Fire"],
                // ["name" => "Amphitheater"],
                // ["name" => "Skating"],
                ["name" => "Cafeteria"],
                ["name" => "Library"],
                ["name" => "Theatre"],
                ["name" => "Spa"],
                ["name" => "ATM"],
                ["name" => "Internet"],
                ["name" => "Grocery"],
                // ["name" => "Solar"],
                ["name" => "Parking"],
                // ["name" => "Covered"],
                // ["name" => "Pet"],
                // ["name" => "Barbecue"],
                // ["name" => "Business"],
                // ["name" => "Waste"],
                ["name" => "Laundry"],
                // ["name" => "Car"],
                // ["name" => "Cycling"],
                // ["name" => "Cricket"],
                // ["name" => "Football"],
                // ["name" => "Volleyball"],
                // ["name" => "Billiards"],
            ];
            $uniqueStates = DB::table('states')
                ->select('state')
                ->distinct()
                ->orderBy('state', 'asc')
                ->pluck('state');

            superAdminLog('info', 'Societies fetched successfully');

            return view(
                'superadmin::settings.settings',
                [
                    'userDetails' => $userDetail,
                    'societies' => $societies,
                    'societySearchTerm' => $Filter_societyName,
                    'admins' => $admins,
                    'roleOfAdmins' => $roleOfAdmins,
                    'amenities' => $amenities,
                    'society' => $request->society,
                    'society_name' => $request->society_name,
                    'stateList' => $uniqueStates,
                    // 'querystringArray' => $querystringArray,
                ]
            );
        } catch (Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());

            $message = (!env('APP_DEBUG'))
                ? 'An error occurred. Please try again later.'
                : $e->getMessage();

            return redirect()->back()->with(['status' => 'error', 'message' => $message]);
        }
    }

    public function updateProfile(Request $request)
    {
        \DB::beginTransaction();
        try {
            superAdminLog('info', 'Entering updateProfile method');
            if (Auth::check()) {
                $user = Auth::user();
                $id = $user->id;

                if ($request->type == 'profileUpdate') {
                    superAdminLog('info', 'activity::profileUpdate');

                    $userDetails = User::find($id);
                    $oldImg = $userDetails->image;

                    if (!$userDetails) {
                        superAdminLog('error', 'User not found');
                        return response()->json(
                            [
                                'status' => 'error',
                                'message' => 'User not found'
                            ]
                        );
                    }

                    if ($request->has('name')) {
                        $userDetails->name = $request->name;
                    }
                    if ($request->has('phone')) {
                        $userDetails->phone = $request->phone;
                    }
                    if ($request->has('email')) {
                        $userDetails->email = $request->email;
                    }

                    // check phone & email is already exists
                    $isEmailExists = User::where('email', $request->email)->where('id', '!=', $id)->first();
                    if ($isEmailExists) {
                        superAdminLog('warning', 'This email already exists - ' . $request->email);
                        return response()->json(['status' => 'warning', 'message' => 'This email already exists.']);
                    }

                    $isPhoneExists = User::where('phone', $request->phone)->where('id', '!=', $id)->first();
                    if ($isPhoneExists) {
                        superAdminLog('warning', 'This phone already exists - ' . $request->phone);
                        return response()->json(['status' => 'warning', 'message' => 'This phone already exists.']);
                    }

                    if ($request->hasFile('profile_picture') && $request->hasFile('profile_picture') != null && $request->isNewProfilePic == 1) {
                        ini_set('upload_max_filesize', '5M');
                        ini_set('post_max_size', '5M');

                        $request->validate([
                            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5120 KB = 5 MB
                        ]);

                        $path = $request->file('profile_picture')->store('profile_picture', 'public');

                        if ($oldImg && Storage::disk('public')->exists($oldImg)) {
                            Storage::disk('public')->delete($oldImg);
                        }

                        $userDetails->image = $path;
                        superAdminLog('info', 'User image uploaded- ' . $path);
                    } else if ($request->imageref == 'yes') {
                        $userDetails->image = null;
                        superAdminLog('info', 'User image null');
                    }

                    if ($userDetails->save()) {
                        //update also member table information
                        $members = Member::where('user_id', $id)->get();
                        //check when $members exist and not empty
                        if ($members) {
                            foreach ($members as $member) {
                                $member->name = $request->name;
                                $member->phone = $request->phone;
                                $member->email = $request->email;
                                $member->save();
                            }
                            superAdminLog('info', 'Member Updated successfully');
                        }
                        \DB::commit();
                        superAdminLog('info', 'Profile updated successfully ');
                        return response()->json(['status' => 'success', 'message' => 'Profile updated successfully']);
                    } else {
                        superAdminLog('error', 'Failed to update profile ');
                        return response()->json(['status' => 'error', 'message' => 'Failed to update profile']);
                    }

                } elseif ($request->type == 'passwordUpdate') {
                    superAdminLog('info', 'activity::passwordUpdate');
                    $userDetails = User::find($id);
                    if (!$userDetails) {
                        superAdminLog('error', 'User not found');
                        return response()->json(['status' => 'error', 'message' => 'User not found']);
                    }
                    if ($request->has('currPass')) {
                        if (Hash::check($request->currPass, $userDetails->password)) {
                            if ($request->has('cnfNewPass')) {
                                $userDetails->password = bcrypt($request->cnfNewPass);
                                $userDetails->remember_token = null;
                                if ($userDetails->save()) {
                                    \DB::commit();
                                    superAdminLog('info', 'Password updated successfully ');
                                    return response()->json(['status' => 'success', 'message' => 'Password updated successfully']);
                                } else {
                                    superAdminLog('error', 'Failed to update password');
                                    return response()->json(['status' => 'error', 'message' => 'Failed to update password']);
                                }
                            } else {
                                superAdminLog('error', 'Missing new password');
                                return response()->json(['status' => 'error', 'message' => 'Missing new password']);
                            }
                        } else {
                            superAdminLog('error', 'Invalid current password');
                            return response()->json(['status' => 'error', 'message' => 'Invalid current password']);
                        }
                    } else {
                        superAdminLog('error', 'Missing current password');
                        return response()->json(['status' => 'error', 'message' => 'Missing current password']);
                    }
                }
            } else {
                superAdminLog('error', 'User not logged in');
                return response()->json(['status' => 'error', 'message' => 'User not logged in']);
            }

        } catch (Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());

            $message = (!env('APP_DEBUG'))
                ? 'An error occurred. Please try again later.'
                : $e->getMessage();

            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }

    public function updatePermission(Request $request)
    {
        \DB::beginTransaction();
        try {
            superAdminLog('info', 'Entering updatePermission method');

            // Convert `is_checked` to boolean
            $request->merge(['is_checked' => filter_var($request->is_checked, FILTER_VALIDATE_BOOLEAN)]);


            $request->validate([
                'user_id' => 'required|exists:users,id',
                'permission' => 'required|string',
                'is_checked' => 'required|boolean',
            ]);

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found']);
            }
            // Check if permission exists or create it
            $permission = Permission::firstOrCreate(['name' => $request->permission]);

            if ($request->is_checked) {
                // Assign permission to user
                $user->givePermissionTo($permission);
                \DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Permission added successfully']);
            } else {
                // Revoke permission from user
                $user->revokePermissionTo($permission);
                \DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Permission removed successfully']);

            }

        } catch (Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());

            $message = (!env('APP_DEBUG'))
                ? 'An error occurred. Please try again later.'
                : $e->getMessage();

            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }

    public function bulkUpdatePermissions(Request $request)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'permissions' => 'required|array',
                'permissions.*.permission' => 'required|string',
                'permissions.*.is_checked' => 'required|boolean',
            ]);

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found']);
            }

            foreach ($request->permissions as $permissionData) {
                $permission = Permission::firstOrCreate(['name' => $permissionData['permission']]);

                if ($permissionData['is_checked']) {
                    // Assign permission
                    $user->givePermissionTo($permission);
                } else {
                    // Revoke permission
                    $user->revokePermissionTo($permission);
                }
            }

            \DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Permissions updated successfully']);
        } catch (Exception $e) {
            \DB::rollBack();
            $message = env('APP_DEBUG') ? $e->getMessage() : 'An error occurred. Please try again later.';
            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }


    public function getUserPermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $permissions = $user->getPermissionNames(); // Returns an array of permission names

        return response()->json([
            'status' => 'success',
            'permissions' => $permissions,
        ]);
    }

    public function showLinkRequestForm()
    {
        return view('superadmin::auth.forgot');
    }

    public function sendResetLinkEmail(Request $request)
    {
        \DB::beginTransaction();
        try {
            superAdminLog('info', 'Entering sendResetLinkEmail method');
            $request->validate(['email' => 'required|email|exists:users,email']);

            $user = User::where('email', $request->email)->first();
            $token = Str::random(60);

            // store the token
            $existingToken = \DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->first();

            if (!$existingToken) {
                \DB::table('password_reset_tokens')->insert([
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => now(),
                ]);
            } else {
                \DB::table('password_reset_tokens')
                    ->where('email', $user->email)
                    ->update([
                        'token' => $token,
                        'created_at' => now(),
                    ]);
            }

            // send email with the reset link
            Mail::send('superadmin::auth.email_template', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Password Reset Link');
            });
            superAdminLog('info', 'Password reset link sent successfully');
            \DB::commit();
            return back()->with(['status' => 'success', 'message' => 'Password reset link sent!']);
        } catch (Exception $e) {
            \DB::rollBack();
            superAdminLog('error', 'Exception::', $e->getMessage());

            $message = (!env('APP_DEBUG'))
                ? 'An error occurred. Please try again later.'
                : $e->getMessage();

            return redirect()->back()->with(['status' => 'error', 'message' => $message]);
        }
    }

    public function showResetForm($token)
    {
        return view('superadmin::auth.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        \DB::beginTransaction();
        try {
            superAdminLog('info', 'Entering reset method');
            $request->validate([
                'token' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);

            $passwordReset = \DB::table('password_reset_tokens')->where('token', $request->token)->first();
            if (!$passwordReset) {
                // Set the error message
                $message = 'Reset password link expired.';

                // Redirect back with the error message
                return redirect()->back()->with(['status' => 'error', 'message' => $message]);
            }

            // Update the user's password
            $user = User::where('email', $passwordReset->email)->first();
            if (!$user) {
                // Set the error message
                $message = 'User not found.';

                // Redirect back with the error message
                return redirect()->back()->with(['status' => 'error', 'message' => $message]);
            }
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the token after use
            \DB::table('password_reset_tokens')->where('token', $request->token)->delete();

            // Log the user in
            auth()->login($user);
            \DB::commit();
            superAdminLog('info', 'Successfully reset and logged in');
            return redirect()->route('superadmin.dashboard')->with(['status' => 'success', 'message' => 'Welcome ' . $user->name]);
        } catch (Exception $e) {
            \DB::rollBack();
            superAdminLog('error', 'Exception::', $e->getMessage());

            $message = (!env('APP_DEBUG'))
                ? 'An error occurred. Please try again later.'
                : $e->getMessage();

            return redirect()->back()->with(['status' => 'error', 'message' => $message]);
        }
    }

    public function checkPhone(Request $request)
    {
        $phone = $request->phone;
        if ($request->userId !== null) {
            $isPhoneExists =
                User::where('phone', $phone)
                    ->where('id', '!=', $request->userId)
                    ->first();
        } else {

            $isPhoneExists =
                User::where('phone', $phone)
                    ->first();
        }

        if ($isPhoneExists) {
            return response()->json([
                'success' => true,
                'message' => 'Already exists !'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Available'
            ]);
        }
    }
    public function checkEmail(Request $request)
    {
        $email = $request->email;
        $isEmailExists = User::where('email', $email)->first();
        if ($request->userId !== null) {
            $isEmailExists =
                User::where('email', $email)
                    ->where('id', '!=', $request->userId)
                    ->first();
        } else {

            $isEmailExists =
                User::where('email', $email)
                    ->first();
        }
        if ($isEmailExists) {
            return response()->json([
                'success' => true,
                'message' => 'Already exists!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Available'
            ]);
        }
    }

    public function checkApertmentNo(Request $request)
    {
        $society_id = getSelectedSociety($request);

        $aprt_no = $request->apert_no;
        $isAprtnoExists = Member::where('society_id', $society_id)->where('aprt_no', $aprt_no)->first();
        if ($request->userId !== null) {
            $isAprtnoExists =
                Member::where('society_id', $society_id)
                    ->where('aprt_no', $aprt_no)
                    ->where('user_id', '!=', $request->userId)
                    ->first();
        } else {

            $isAprtnoExists =
                Member::where('society_id', $society_id)
                    ->where('aprt_no', $aprt_no)
                    ->first();
        }
        if ($isAprtnoExists) {
            return response()->json([
                'success' => true,
                'message' => 'Already occupied!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Available'
            ]);
        }
    }

    public function isAlreadyAdminExist(Request $request)
    {
        if ($request->userId !== null) {

            $isAdminExists = Member::where('user_id', '!=', $request->userId)
                ->where('society_id', $request->society_id)
                ->where('role', 'admin')
                ->exists();

        } else {

            $isAdminExists = Member::where('society_id', $request->society_id)
                ->where('role', 'admin')
                ->exists();
        }

        if ($isAdminExists) {
            return response()->json([
                'success' => true,
                'message' => 'Already Exist !',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Available',
            ]);
        }
    }

    public function checkVacancy(Request $request)
    {
        $society_id = getSelectedSociety($request);

        if (empty($society_id)) {
            $society_id = auth()->user()->member->society_id;
        }

        $societyInfo = Society::find($society_id);
        $floorUnitSpace = $societyInfo->floor_units;

        $blockInfo = Block::select('name')
            ->where('id', $request->block_id)
            ->where('society_id', $society_id)
            ->first();

        $blockIds = Block::where('name', $blockInfo->name)
            ->where('society_id', $society_id)
            ->pluck('id')
            ->toArray();

        $getBlockInfo = Block::select('unit_qty', 'total_units')
            ->where('society_id', $society_id)
            ->whereIn('id', $blockIds)
            ->where('unit_type', $request->unit_type)
            ->get();

        // $totalBlocksOnSociety = Block::select('name')
        //     ->distinct()
        //     ->where('society_id', $society_id)
        //     ->get();

        foreach ($getBlockInfo as $blockd) {

            $unitTypeSpace = $blockd->unit_qty;
            $blockSpace = $blockd->total_units;
        }


        $getAllUserCount = Member::
            where('society_id', $society_id)
            // ->where('floor_number', $request->floor_number)
            ->where('unit_type', $request->unit_type)
            ->whereIn('block_id', $blockIds)//b'coz same block name can be many different id (block name is with different unit_type)
            ->count();

        if ($request->user_id !== null) {

            // get current member occupied [super-admin not in society]
            $getCurrentUserCount = Member::
                where('society_id', $society_id)
                ->where('user_id', $request->user_id)
                // ->where('floor_number', $request->floor_number)
                ->where('unit_type', $request->unit_type)
                ->whereIn('block_id', $blockIds)//b'coz same block name can be many different id (block name is with different unit_type)
                ->count();
            $getAllUserCount = $getAllUserCount - $getCurrentUserCount;
        }

        // $data = "getAllUserCount-" . $getAllUserCount . ",unitTypeSpace-" . $unitTypeSpace . ',floorUnitSpace-' . $floorUnitSpace . ",blockSpace-" . $blockSpace;
        // return response()->json([
        //     'success' => true,
        //     'message' => 'No vacancy available,  ' . $data
        // ]);
        if (
            $getAllUserCount < $unitTypeSpace &&
            $getAllUserCount < $floorUnitSpace &&
            $getAllUserCount < $blockSpace
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Vacancy available'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No vacancy available'
            ]);
        }

    }

    public function getStateByPlaceMatch(Request $request)
    {
        $place = $request->input('place');

        $state = DB::table('states')
            ->where('place', 'LIKE', '%' . $place . '%')
            ->value('state'); // Get the state name

        return response()->json(['state' => $state]);
    }
}
