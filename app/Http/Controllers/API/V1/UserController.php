<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\ComplaintCategory;
use App\Models\Member;
use App\Models\ServiceProviders;
use App\Models\Society;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function sendOtp(Request $request)
    {
        try {

            $getSciety = Society::where('id', $request->society_id)
                ->first();

            if (!$getSciety) {
                return res(
                    status: false,
                    message: "Society not found !",
                    code: HTTP_NOT_FOUND
                );
            }

            if (Auth::check()) {
                $user = Auth::user();
                $user->currentAccessToken()->delete();
            }

            $validator = validator($request->all(), [
                'phone' => 'required|string|min:10|max:10',
                'society_id' => 'required',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user = User::with('member')->where('phone', $request->phone)->first();

            if (!$user) {
                return res(
                    status: false,
                    message: "Phone number not exist !",
                    code: HTTP_NOT_FOUND
                );
            }


            if ($user->role == 'super_admin' || $user->role == 'guest' || $user->role == 'service_provider') {
                return res(
                    status: false,
                    message: "Unauthorized Access !",
                    code: HTTP_UNAUTHORIZED
                );
            }


            $loggerRole = $user->role;
            // if ($loggerRole == 'admin') {
            //     // allowing admin login, replacing admin role as resident
            //     $user->original_role = $user->role;
            //     $user->role = 'resident';
            //     $loggerRole = 'resident';
            //     // return $user;
            // }

            $getDetail = [];

            if ($loggerRole == 'resident' || $loggerRole == 'admin') {

                $getDetail = Member::where('user_id', $user->id)
                    ->first();
            }
            // elseif ($loggerRole == 'service_provider') {
            //     $getDetail = ServiceProviders::where('user_id', $user->id)
            //         ->first();
            // }
            elseif (
                $loggerRole == 'staff' || $loggerRole == 'staff_security_guard'
            ) {
                $getDetail = Staff::where('user_id', $user->id)->first();
            }

            if (!$getDetail) {
                return res(
                    status: false,
                    message: "No records found !",
                    code: HTTP_NOT_FOUND
                );
            }

            if ($user->status == 'inactive') {
                return res(
                    status: false,
                    message: "Your account is inactive !",
                    code: HTTP_FORBIDDEN
                );
            }

            if ($getDetail->society_id != $request->society_id) {
                return res(
                    status: false,
                    message: "Please choose your own society !",
                    code: HTTP_FORBIDDEN
                );
            }

            // Generate a 4-digit OTP
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Store the hashed OTP in the user's 'otp' column
            $user->otp = 5555;
            $user->otp_expire_time = Carbon::now()->addMinutes(2);
            $user->save();
            $data = [
                'otp' => $user->otp,
            ];

            return res(
                status: true,
                message: "Otp sent successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function sendOtp_withOtpAPI(Request $request)
    {
        try {

            $getSciety = Society::where('id', $request->society_id)
                ->first();

            if (!$getSciety) {
                return res(
                    status: false,
                    message: "Society not found !",
                    code: HTTP_NOT_FOUND
                );
            }

            if (Auth::check()) {
                $user = Auth::user();
                $user->currentAccessToken()->delete();
            }

            $validator = validator($request->all(), [
                'phone' => 'required|string|min:10|max:10',
                'society_id' => 'required',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user = User::with('member')->where('phone', $request->phone)->first();

            if (!$user) {
                return res(
                    status: false,
                    message: "Phone number not exist !",
                    code: HTTP_NOT_FOUND
                );
            }


            if ($user->role == 'super_admin' || $user->role == 'guest' || $user->role == 'service_provider') {
                return res(
                    status: false,
                    message: "Unauthorized Access !",
                    code: HTTP_UNAUTHORIZED
                );
            }


            $loggerRole = $user->role;
            // if ($loggerRole == 'admin') {
            //     // allowing admin login, replacing admin role as resident
            //     $user->original_role = $user->role;
            //     $user->role = 'resident';
            //     $loggerRole = 'resident';
            //     // return $user;
            // }

            $getDetail = [];

            if ($loggerRole == 'resident' || $loggerRole == 'admin') {

                $getDetail = Member::where('user_id', $user->id)
                    ->first();
            }
            // elseif ($loggerRole == 'service_provider') {
            //     $getDetail = ServiceProviders::where('user_id', $user->id)
            //         ->first();
            // }
            elseif (
                $loggerRole == 'staff' || $loggerRole == 'staff_security_guard'
            ) {
                $getDetail = Staff::where('user_id', $user->id)->first();
            }

            if (!$getDetail) {
                return res(
                    status: false,
                    message: "No records found !",
                    code: HTTP_NOT_FOUND
                );
            }

            if ($user->status == 'inactive') {
                return res(
                    status: false,
                    message: "Your account is inactive !",
                    code: HTTP_FORBIDDEN
                );
            }

            if ($getDetail->society_id != $request->society_id) {
                return res(
                    status: false,
                    message: "Please choose your own society !",
                    code: HTTP_FORBIDDEN
                );
            }

            // Generate a 4-digit OTP
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Store the hashed OTP in the user's 'otp' column
            $user->otp = 5555;
            $user->otp_expire_time = Carbon::now()->addMinutes(10);

            $payload = [
                "template_id" => "65e06f45d6fc05278c7d47e2",
                "recipients" => [
                    [
                        "mobiles" => '91' . $request->phone,
                        "var1" => $user->otp
                    ]
                ]
            ];

            $headers = [
                'authkey' => '416091ALVB8qpT65cb570bP1',
                'Content-Type' => 'application/json'
            ];
            $response = Http::withHeaders($headers)->post('https://control.msg91.com/api/v5/flow', $payload);
            \Log::info("otpSendResponse, response: " . json_encode($response));
            if ($response->successful()) {

                $user->save();

                $data = [
                    'otp' => $user->otp,
                ];

                return res(
                    status: true,
                    message: "Otp sent successfully",
                    data: $data,
                    code: HTTP_OK
                );
                // return response()->json(['success' => true]);
            } else {

                return res(
                    status: false,
                    message: "Failed to sent OTP, Please try again",
                    // data: $data,
                    code: HTTP_INTERNAL_SERVER_ERROR
                );
            }

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function otpVerifyLogin(Request $request)
    {
        try {

            $validator = validator($request->all(), [
                'phone' => 'required|string|min:10|max:10',
                'otp' => 'required|min:4|max:4',
                'device_id' => 'required|string'
                // 'device_id' => 'required|string|unique:users,device_id'
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $otp_expire_time = Carbon::now();

            $user = User::where([
                ['phone', '=', $request->phone],
                ['otp', '=', $request->otp],
            ])->first();

            if (!$user) {
                return res(
                    status: false,
                    message: "OTP does not match or invalid phone number !",
                    code: HTTP_NOT_FOUND
                );
            }

            // Check if OTP has expired
            if ($user->otp_expire_time < $otp_expire_time) {
                return res(
                    status: false,
                    message: "OTP has expired !",
                    code: HTTP_FORBIDDEN
                );
            }

            // Revoke all of the user's tokens (log out from all devices)
            // $user->tokens->each(function ($token) {
            //     $token->delete();
            // });

            Auth::login($user, true);
            // Auth::login($user);
            $token = $user->createToken('login')->plainTextToken;
            $role = $user->role;
            if ($user->image != NULL) {
                $user->image = url('storage/' . $user->image);
            }
            // Remove the OTP and OTP expiration time from the user
            $user->otp = NULL;
            $user->otp_expire_time = NULL;
            // $user->save();
            $user->makeHidden(['member', 'service_provider', 'staff']);

            // save user app device id
            // auth()->user()->update(['device_id' => $request->device_id]);
            \DB::update('update users set device_id = ? where id = ?', [$request->device_id, auth()->id()]);

            $data = [
                'token' => $token,
                'role' => $role,
                'user' => $user,
            ];

            return res(
                status: true,
                message: "Logged in successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getProfile($user_id = null)
    {
        try {
            if ($user_id == null) {
                $user = Auth::user();
                $user = User::find($user->id);
            } else {
                $user = User::find($user_id);
            }
            if (!$user) {
                return res(
                    status: false,
                    message: "User not found !",
                    code: HTTP_NOT_FOUND
                );
            }
            $user->makeHidden(['member', 'serviceProvider', 'staff']);

            $getSocietyInfo = Society::find($user->society_id);
            $user->society_name = $getSocietyInfo->name;

            if ($user->role == 'resident' || $user->role == 'admin') {

                $getBlockInfo = Block::find($user->member->block_id);

                $user->unit_type = $user->member->unit_type;
                $user->floor_number = $user->member->floor_number;
                $user->block_name = $getBlockInfo->name;
                $user->aprt_no = $user->member->aprt_no;
                $user->block_info = $getBlockInfo;
            } else {
                // assign as null
                $user->unit_type = null;
                $user->floor_number = null;
                $user->block_name = null;
                $user->aprt_no = null;
            }

            if ($user->role == 'staff') {

                $categoryInfo = ComplaintCategory::find($user->staff->complaint_category_id);

                $user->category_name = $categoryInfo->name;
                $user->category_id = $user->staff->complaint_category_id;
            } else {
                // assign as null
                $user->category_name = null;
                $user->category_id = null;
            }

            if ($user->image != null) {
                $user->image = url('storage/' . $user->image);
            }

            $data = [
                'user' => $user,
                'unpaid_bills' => $user->myUnpaidBills
            ];
            return res(
                status: true,
                message: "Profile data retrieved successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return res(
                status: true,
                message: "User Logout successfully",
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    public function updateProfile(Request $request)
    {
        \Log::channel('apilog')->info(json_encode([
            'timestamp' => now(),
            'request_data' => request()->all(), // Log the request data
            'url' => request()->url(), // Log the URL where the error occurred
            'method' => request()->method(), // HTTP method
            'headers' => request()->header(), // Headers for deeper debugging
            'ip' => request()->ip(), // Log the client IP
        ]));

        // Begin a transaction
        \DB::beginTransaction();
        try {
            $user = Auth::user();

            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:10|min:10|unique:users,phone,' . $user->id,
                'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($request->hasFile('profile_picture')) {
                ini_set('upload_max_filesize', '5M');
                ini_set('post_max_size', '5M');

                // Store the new profile picture
                $oldImg = $user->image; // Keep reference to the old image
                $path = $request->file('profile_picture')->store('profile_picture', 'public');

                // Delete old image if exists
                if ($oldImg && \Storage::disk('public')->exists($oldImg)) {
                    \Storage::disk('public')->delete($oldImg);
                }
                // Update user's image path
                $user->image = $path;
            }

            // Update the user's table
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            if ($user->role == 'resident' || $user->role == 'admin') {

                // Update the members table through the relationship
                $member = $user->member;  // Assuming the relationship is defined in the User model
                if ($member) {
                    $member->name = $request->name;
                    $member->email = $request->email;
                    $member->phone = $request->phone;
                    $member->save();
                }
            }
            //  elseif ($user->role == 'service_provider') {
            //     $serviceProvider = $user->serviceProvider;

            //     if ($serviceProvider) {
            //         $serviceProvider->name = $request->name;
            //         $serviceProvider->email = $request->email;
            //         $serviceProvider->phone = $request->phone;
            //         $serviceProvider->save();
            //     }
            // }
            elseif (strpos($user->role, 'staff') === 0) {// Matches any role starting with 'staff'
                $staff = $user->staff;

                if ($staff) {
                    $staff->name = $request->name;
                    $staff->email = $request->email;
                    $staff->phone = $request->phone;
                    $staff->save();
                }
            }

            // Commit the transaction
            \DB::commit();

            $user->makeHidden(['member', 'serviceProvider', 'staff']);
            if ($user->image != null) {
                $user->image = url('storage/' . $user->image);
            }
            $data = [
                'user' => $user,
            ];
            return res(
                status: true,
                message: "Profile updated successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            // Rollback in case of an error
            \DB::rollBack();

            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function changePassword(Request $request)
    {
        \DB::beginTransaction();
        try {
            $user = Auth::user();

            if (!$user) {
                return res(
                    status: false,
                    message: 'User not logged in',
                    code: HTTP_UNAUTHORIZED
                );
            }

            // Validate request data
            $validator = validator($request->all(), [
                'currPass' => 'required|string',
                'newPass' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Check if the current password matches
            if (!Hash::check($request->currPass, $user->password)) {
                return res(
                    status: false,
                    message: 'Invalid current password',
                    code: HTTP_UNAUTHORIZED
                );
            }

            // Update the password
            $user->password = Hash::make($request->newPass);
            $user->remember_token = null; // Invalidate old sessions or tokens

            if ($user->save()) {
                \DB::commit(); // Commit the transaction

                return res(
                    status: true,
                    message: 'Password updated successfully',
                    code: HTTP_OK
                );
            }

        } catch (\Exception $e) {
            // Rollback on error
            \DB::rollBack();

            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function passwordLogin(Request $request)
    {
        try {
            // Validate input
            $validator = validator($request->all(), [
                'phone' => 'required|string|min:10|max:10',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find user by phone number
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return res(
                    status: false,
                    message: "Invalid phone number!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return res(
                    status: false,
                    message: "Password is incorrect!",
                    code: HTTP_UNAUTHORIZED
                );
            }

            // Log the user in and generate token
            Auth::login($user, true);
            $token = $user->createToken('login')->plainTextToken;

            // Check for user image
            if ($user->image != null) {
                $user->image = url('storage/' . $user->image);
            }

            $user->makeHidden(['member']);

            $data = [
                'token' => $token,
                'role' => $user->role,
                'user' => $user,
            ];

            return res(
                status: true,
                message: "User logged in successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function notificationList(Request $request)
    {
        try {

            // Fetch unread notifications count
            $unreadCount = \DB::table('notifications')
                ->where('type', 'pushNotification')
                ->where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', auth()->id())
                ->whereNull('read_at') // Count only unread notifications
                ->count();

            $notifications = \DB::table('notifications')
                ->select('id', 'read_at', 'type', 'data AS notification', 'created_at')
                ->where('type', 'pushNotification')
                ->where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10); // Paginate with 10 items per page

            // Check if notices are found
            if ($notifications->isEmpty()) {
                return res(
                    status: false,
                    message: "No notification found!",
                    code: HTTP_OK
                );
            }

            // Decode the JSON for each notification
            $notifications->getCollection()->transform(function ($notification) {
                $notification->notification = json_decode($notification->notification, true);
                return $notification;
            });

            // Return the response in JSON format
            $data = [
                'notifications' => $notifications,
                'total_unread_notifications' => $unreadCount,
            ];
            return res(
                status: true,
                message: "notifications retrived successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function markAsReadNotification(Request $request)
    {
        try {

            $validator = validator($request->all(), [
                'notification_id' => 'required_if:all_read,false|exists:notifications,id', // Make notification_id required only when all_read is false
                'all_read' => 'nullable|boolean', // Optional all_read flag
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $message = '';

            if ($request->all_read === true) {
                // Update all notifications for the specified user to be marked as read
                \DB::table('notifications')
                    ->where('notifiable_type', 'App\Models\User')
                    ->where('notifiable_id', auth()->id())
                    ->update(['read_at' => now()]);

                $message = 'All notifications marked as read successfully';
            } else {
                // Fetch the notification for the authenticated user
                $notification = \DB::table('notifications')
                    ->where('id', $request->notification_id)
                    ->where('notifiable_type', 'App\Models\User')
                    ->where('notifiable_id', auth()->id())
                    ->first();

                // Check if notification exists
                if (!$notification) {
                    return res(
                        status: false,
                        message: "Notification not found!",
                        code: HTTP_NOT_FOUND
                    );
                }

                // Update the read_at timestamp
                \DB::table('notifications')
                    ->where('id', $request->notification_id)
                    ->update(['read_at' => now()]);

                $message = "Notification marked as read successfully";
            }

            return res(
                status: true,
                message: $message,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            // Handle any exceptions
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


}
