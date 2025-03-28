<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Models\Member;
use App\Models\SosCategory;
use App\Models\Sos;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\DynamicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SosController extends Controller
{
    public function getSosCategoriesOLD()
    {
        try {
            $sos_categories = SosCategory::with([
                'emergencyDetails' => function ($query) {
                    $query->whereIn('type', ['action', 'contact'])
                        ->orderByRaw("FIELD(type, 'action', 'contact')");
                }
            ])->get();

            // Prepare the data in the desired format
            $sos_categories_data = $sos_categories->map(function ($category) {
                $action_details = $category->emergencyDetails->where('type', 'action')->first();
                $contact_details = $category->emergencyDetails->where('type', 'contact')->first();

                return [
                    'category' => $category,
                    'action_details' => $action_details ?: null,
                    'contact_details' => $contact_details ?: null,
                ];
            });

            // $sos_categories = SosCategory::all();
            // Check if not found
            if ($sos_categories->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $data = [
                'sos_categories' => $sos_categories,
            ];
            return res(
                status: true,
                message: "Data retrieved successfully",
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

    public function getSosCategories1()
    {
        try {
            // Fetch categories with related emergency details
            $sos_categories = SosCategory::with([
                'emergencyDetails' => function ($query) {
                    $query->whereIn('type', ['action', 'contact'])
                        ->orderByRaw("FIELD(type, 'action', 'contact')");
                }
            ])->get();

            // Check if categories are found
            if ($sos_categories->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Prepare the data in the desired format
            $sos_categories_data = $sos_categories->map(function ($category) {
                // Separate actions and contacts
                $actions = $category->emergencyDetails->where('type', 'action')->values();
                $contacts = $category->emergencyDetails->where('type', 'contact')->values();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image,
                    'emergency_details' => [
                        'actions' => $actions,  // Filtered actions
                        'contacts' => $contacts  // Filtered contacts
                    ]
                ];
            });

            // Response data
            $data = [
                'sos_categories' => $sos_categories_data,
            ];

            return res(
                status: true,
                message: "Data retrieved successfully",
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

    public function getSosCategories()
    {
        try {
            // Fetch categories with related emergency details
            $sos_categories = SosCategory::with([
                'emergencyDetails' => function ($query) {
                    $query->whereIn('type', ['action', 'contact'])
                        ->orderByRaw("FIELD(type, 'action', 'contact')");
                }
            ])->get();

            // Check if categories are found
            if ($sos_categories->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Prepare the data in the desired format
            $sos_categories_data = $sos_categories->map(function ($category) {
                // Separate actions and contacts
                $actions = $category->emergencyDetails->where('type', 'action')->values()->map(function ($action) {
                    // Remove the phone field for actions
                    unset($action->phone);
                    return $action;
                });

                $contacts = $category->emergencyDetails->where('type', 'contact')->values();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image,
                    'emergency_details' => [
                        'actions' => $actions,  // Filtered actions without phone
                        'contacts' => $contacts  // Filtered contacts with phone
                    ]
                ];
            });

            // Response data
            $data = [
                'sos_categories' => $sos_categories_data,
            ];

            return res(
                status: true,
                message: "Data retrieved successfully",
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



    public function elements()
    {
        try {

            $areas = [
                ["name" => "Kitchen"],
                ["name" => "Living Room"],
                ["name" => "Bedroom"],
                ["name" => "Guest Room"],
                ["name" => "Balcony"],
                ["name" => "Dining Room"],
                ["name" => "Bathroom"],
                ["name" => "Study Room"],
                ["name" => "Laundry Room"],
                ["name" => "Storage Room"],
                ["name" => "Garage"],
                ["name" => "Terrace"],
                ["name" => "Garden"],
                ["name" => "Corridor"],
                ["name" => "Gym Room"],
                ["name" => "Play Room"],
                ["name" => "Home Office"],
                ["name" => "Pantry"],
                ["name" => "Attic"],
                ["name" => "Basement"],
                ["name" => "Servant Quarters"],
                ["name" => "Pool Area"],
                ["name" => "Parking Lot"],
                ["name" => "Clubhouse"],
                ["name" => "Library Room"],
                ["name" => "Roof Deck"],
                ["name" => "Conference Room"],
                ["name" => "Security Office"],
                ["name" => "Entertainment Room"],
                ["name" => "Barbecue Area"],
                ["name" => "Community Hall"],
                ["name" => "Game Room"],
                ["name" => "Cinema Room"],
                ["name" => "Pet Zone"],
                ["name" => "Spa Room"],
                ["name" => "Maintenance Office"]
            ];

            usort($areas, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            $data = [
                'areas' => $areas,
            ];
            return res(
                status: true,
                message: "Form dropdowns retrieved successfully",
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

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'sos_category_id' => 'required|string',
                'area' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            //get member information
            $alert_by = auth()->id();
            $phone = auth()->user()->phone;
            $members = User::with('member')
                ->where('id', $alert_by)
                ->first();

            $members = $members->member;

            $dateTime = Carbon::now('Asia/Kolkata');
            // Get the current date
            $currentDate = $dateTime->toDateString(); // e.g., "2024-10-17"
            // Get the current time
            $currentTime = $dateTime->toTimeString(); // e.g., "15:45:30"

            // Create new
            if (auth()->user()->role == 'resident' || auth()->user()->role == 'admin') {
                $insertData = [
                    ...$request->all(),
                    'alert_by' => $alert_by,
                    'society_id' => auth()->user()->society_id,
                    'block_id' => $members->block_id,
                    'phone' => $members->phone,
                    'floor' => $members->floor_number,
                    'unit_no' => $members->aprt_no,
                    'unit_type' => $members->unit_type,
                    'date' => $currentDate,
                    'time' => $currentTime,
                ];
            } elseif (auth()->user()->role == 'staff_security_guard') {
                $insertData = [
                    ...$request->all(),
                    'alert_by' => $alert_by,
                    'society_id' => auth()->user()->society_id,
                    // 'block_id' => $members->block_id,
                    'phone' => $phone,
                    // 'floor' => $members->floor_number,
                    // 'unit_no' => $members->aprt_no,
                    // 'unit_type' => $members->unit_type,
                    'date' => $currentDate,
                    'time' => $currentTime,
                ];
            }

            $sosAlert = Sos::create($insertData);

            $sosAlert = Sos::with('user', 'society')->find($sosAlert->id);

            // ================================================
            // Send notifications to Admin (Panel)
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'sos.';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($sosAlert) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $sosAlert->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);

            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                $checkSettings = 'sos_notifications';
                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'Emergency SOS Alert',
                            'body' => $sosAlert->user->name . " has sent an SOS. Please check the details immediately",
                            'model' => 'Sos',
                            'model_id' => $sosAlert->id,
                            'society_name' => $sosAlert->society->name,
                            'society_id' => $sosAlert->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // send push notification to all app security guard
            $query = User::whereHas('staff', function ($query) use ($sosAlert) {
                $query->where('status', 'active')
                    ->where('role', 'staff_security_guard')
                    ->where('society_id', $sosAlert->society_id);
            });

            $userCount = $query->count(); // Get total user count

            $notifyBody = '';

            if ($sosAlert->user->role == 'admin' || $sosAlert->user->role == 'resident') {
                $sosSender = Member::with('block')->where('user_id', $sosAlert->alert_by)->first();

                $notifyBody = $sosAlert->user->name . "( Block : " . $sosSender->block->name . ", Floor : " . $sosSender->block->floor . ", Property Number : " . $sosSender->block->property_number . ", Area - " . $sosAlert->area . " ) has sent an SOS. Description - " . $sosAlert->description . " . Please respond immediately";

            } elseif ($sosAlert->user->role == 'staff_security_guard') {
                $sosSender = Staff::where('user_id', $sosAlert->alert_by)->first();
                $notifyBody = $sosAlert->user->name . "( Area - " . $sosAlert->area . " ) has sent an SOS. Description - " . $sosAlert->description . " . Please respond immediately";
            }

            if ($userCount > 500) { // Use chunk if user count is large
                $query->chunk(200, function ($users) use ($sosAlert, $notifyBody) {
                    foreach ($users as $notifyUser) {
                        if (!empty($notifyUser->device_id)) {
                            $deviceId = $notifyUser->device_id;
                            $notificationMessageArray = [
                                'title' => 'Emergency SOS Alert',
                                'body' => $notifyBody,
                            ];

                            $notificationDataArray = [
                                'type' => 'sos_alert',
                                'sos_id' => $sosAlert->id,
                                'name' => $sosAlert->user->name,
                                'mob' => $sosAlert->user->phone,
                                'user_id' => $sosAlert->user->id,
                                'user_type' => $sosAlert->user->role,
                                'img' => $sosAlert->user->image ?? null,
                                'time' => now()->format('h:iA')
                            ];

                            SendPushNotificationJob::dispatch($notifyUser->id, $deviceId, $notificationMessageArray, $notificationDataArray);
                        }
                    }
                });
            } elseif ($userCount > 0 && $userCount < 500) { // Use get() if user count is small
                $users = $query->get();
                foreach ($users as $notifyUser) {
                    if (!empty($notifyUser->device_id)) {
                        $deviceId = $notifyUser->device_id;

                        $notificationMessageArray = [
                            'title' => 'Emergency SOS Alert',
                            'body' => $notifyBody,
                        ];

                        $notificationDataArray = [
                            'type' => 'sos_alert',
                            'sos_id' => $sosAlert->id,
                            'name' => $sosAlert->user->name,
                            'mob' => $sosAlert->user->phone,
                            'user_id' => $sosAlert->user->id,
                            'user_type' => $sosAlert->user->role,
                            'img' => $sosAlert->user->image ?? null,
                            'time' => now()->format('h:iA')
                        ];
                        \Log::info("<to security guard - message >:::");
                        sendAppPushNotification($notifyUser->id, $deviceId, $notificationMessageArray, $notificationDataArray);
                    }
                }
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            \DB::commit();

            $data = [
                'sent_alert' => $sosAlert,
            ];

            return res(
                status: true,
                message: "Alert sent successfully",
                data: $data,
                code: HTTP_CREATED
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function index(Request $request)
    {
        try {

            $now = Carbon::now('Asia/Kolkata');
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $user_role = auth()->user()->role;

            // Base query
            $query = Sos::with([
                'sosCategory:id,name',
                'user:id,name,phone',
                'society:id,name',
                'block:id,name,floor,property_number,unit_type',
                'acknowledgedBy:id,role,name,phone,image'
            ]);

            if ($user_role == 'admin' || $user_role == 'resident') {

                $query = $query->where('alert_by', $user_id);

            } elseif ($user_role == 'staff_security_guard') {

                $query = $query->where('society_id', $society_id);
            }

            $sos = $query->orderBy('created_at', 'desc')
                ->paginate(25);

            // Check if sos are found
            if ($sos->isEmpty()) {
                return res(
                    status: false,
                    message: "No SOS found!",
                    data: null,
                    code: HTTP_OK
                );
            }

            $data = [
                'sos' => $sos
            ];
            // Return response
            return res(
                status: true,
                message: "SOS retrieved successfully.",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            // Handle exceptions
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function acknowledgeSOS(Request $request)
    {
        try {
            // Validate input
            $validator = validator($request->all(), [
                'sos_id' => 'required|exists:sos,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the alert
            $alert = Sos::find($request->sos_id);
            if (!$alert) {
                return res(
                    status: false,
                    message: "SOS Alert not found.",
                    code: HTTP_NOT_FOUND
                );
            }

            // Mark as acknowledged
            $alert->acknowledged_at = now();
            $alert->acknowledged_by = auth()->id();
            $alert->status = 'acknowledged';
            $alert->save();

            return res(
                status: true,
                message: "SOS alert acknowledged successfully.",
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function cancelSOS(Request $request)
    {
        try {
            // Validate input
            $validator = validator($request->all(), [
                'sos_id' => 'required|exists:sos,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the alert
            $alert = Sos::where('alert_by', auth()->id())->where('id', $request->sos_id)->first();
            if (!$alert) {
                return res(
                    status: false,
                    message: "SOS Alert not found.",
                    code: HTTP_NOT_FOUND
                );
            }

            // Mark as acknowledged
            $alert->status = 'cancelled';
            $alert->save();

            return res(
                status: true,
                message: "SOS cancelled successfully.",
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


}
