<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NotificationSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Member;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Society;
use App\Models\Block;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin::index');
    }
    public function showLoginForm()
    {
        return view('admin::auth.login');
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
        $whenRole = 'admin';
        $credentials['role'] = $whenRole;
        // dd(vars: $credentials);
        // echo 'logged1';
        // dd(Auth::guard('web')->attempt($credentials, $remember));
        // dd(Auth::guard('web')->user());

        // $user = User::where('email', 'mdismail@gmail.com')->where('role', 'admin')->first();
        // echo $hash = Hash::make('12345678');
        // echo '=========' . Hash::check("12345678", $user->password);
        // exit;
        // // dd($request->password);
        // dd($user);
        // if ($user && Hash::check(trim($request->input('password')), $user->password)) {
        //     echo 'Password matches';
        // } else {
        //     echo 'Password does not match';
        // }

        // \DB::enableQueryLog();
        // Auth::attempt($credentials, $remember);
        // dd(\DB::getQueryLog());

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            // echo 'logged2';
            $user = Auth::user();
            // dd($user);
            // echo 'logged3';
            // exit;

            // $society = Society::where('member_id', $user->id)->where('status', 'active')->get();
            // // dd($society);
            // if ($society->isEmpty()) {
            //     Auth::logout();
            //     $request->session()->invalidate();//ensure session clean
            //     $request->session()->regenerateToken();//avoid session fixaction attack

            //     return redirect()->route('admin.login.form')->with(['status' => 'error', 'message' => 'Restricted access! You are not assigned as admin of any society.']);
            // }

            $member = Member::where('user_id', $user->id)->first();

            $societyExist = Society::where('id', $member->society_id)->where('status', 'active')->exists();
            if (!$societyExist) {
                Auth::logout();
                $request->session()->invalidate();//ensure session clean
                $request->session()->regenerateToken();//avoid session fixation attack

                return redirect()->route('admin.login.form')->with(['status' => 'error', 'message' => 'Invalid credentials']);
            }

            if ($member->status == 'inactive') {
                Auth::logout();
                $request->session()->invalidate();//ensure session clean
                $request->session()->regenerateToken();//avoid session fixation attack

                return redirect()->route('admin.login.form')->with(['status' => 'error', 'message' => 'Unauthorized access! Your account is inactive.']);
            }

            if ($user->role == 'admin') {
                // echo 'logged';
                // exit;

                return redirect()->route('admin.dashboard')->with(['status' => 'success', 'message' => 'Welcome ' . $user->name]);
            } else {

                Auth::logout();
                $request->session()->invalidate();//ensure session clean
                $request->session()->regenerateToken();//avoid session fixaction attack

                return redirect()->route('admin.login.form')->with(['status' => 'error', 'message' => 'Unauthorized access']);
            }
        } else {
            // echo 'logged-failed';
            return redirect()->route('admin.login.form')->with(['status' => 'error', 'message' => 'Invalid credentials']);
        }
    }

    public function processLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();//ensure session clean
        $request->session()->regenerateToken();//avoid session fixaction attack
        return redirect()->route('admin.login.form')->with(['status' => 'success', 'message' => 'Logged out successfully']);
    }


    public function dashboard()
    {
        return view('admin::dashboard.dashboard');
    }

    public function profile(Request $request)
    {
        try {
            superAdminLog('info', 'Entering profile method');
            $validator = validator($request->all(), [
                'society_name' => 'sometimes|string|max:255|nullable',
                'status' => 'sometimes|required|string|max:255',
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
            $Filter_status = isset($request->status) ? $request->status : 'active';


            // Fetch societies with filters and pagination
            $societies = Society::with('blocks')
                ->searchByName($Filter_societyName)
                ->searchByStatus($Filter_status)
                ->orderBy('id', 'desc')
                ->paginate(5);

            // Show the SQL query
            // $sql = $societies->toSql();
            // $bindings = $societies->getBindings();

            // dd($sql, $bindings); // Use dd() to dump and die to see the query and bindings
            // $admins = Member::where('status', 'active')
            //     ->where('role', 'admin')
            //     ->whereNotIn('id', function ($query) {
            //         $query->select(\DB::raw('GROUP_CONCAT(member_id)'))
            //             ->from('societies');
            //     })
            //     ->get();
            $admins = Member::where('status', 'active')
                ->where('role', 'admin')
                ->where('role', 'admin')
                ->whereNull('deleted_at')
                ->whereNotExists(function ($query) {
                    $query->select(\DB::raw(1))
                        ->from('societies')
                        ->whereColumn('societies.member_id', 'members.id');
                })
                ->get();

            // get notification settings
            $notificationSettings = NotificationSettings::where('user_id', auth()->id())
                ->where('user_of_system', 'panel')->get();

            // dd($notificationSettings);
            // $querystringArray = $request->except('_token');

            superAdminLog('info', 'Societies fetched successfully');

            return view(
                'admin::settings.settings',
                [
                    'userDetails' => $userDetail,
                    'societies' => $societies,
                    'societySearchTerm' => $Filter_societyName,
                    'admins' => $admins,
                    'notificationSettings' => $notificationSettings,
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

    public function showLinkRequestForm()
    {
        return view('admin::auth.forgot');
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
            Mail::send('admin::auth.email_template', ['token' => $token], function ($message) use ($user) {
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
        return view('admin::auth.reset', ['token' => $token]);
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
            return redirect()->route('admin.dashboard')->with(['status' => 'success', 'message' => 'Welcome ' . $user->name]);
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

        if (empty($society_id)) {
            $society_id = auth()->user()->member->society_id;
        }
        $aprt_no = $request->apert_no;
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

            // get current member occupied
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
}
