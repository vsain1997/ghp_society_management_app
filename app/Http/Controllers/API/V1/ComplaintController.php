<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Models\Block;
use App\Models\Staff;
use App\Notifications\DynamicNotification;
use Carbon\Carbon;
use App\Models\Complaint;
use App\Models\ComplaintFile;
use App\Models\ComplaintCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ComplaintController extends Controller
{
    public function getComplaintCategories()
    {
        try {
            $complaint_categories = ComplaintCategory::all();
            // Check if not found
            if ($complaint_categories->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_OK
                );
            }

            $data = [
                'complaint_categories' => $complaint_categories,
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

    public function store(Request $request)
    {
        \Log::info('Complaint Store Request', ['payload' => $request->all(), 'files' => $request->file()]);

        // Automatically categorize uploaded files into the correct request keys
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {
                if (in_array($file->getClientMimeType(), ['image/jpeg', 'image/jpg', 'image/png'])) {
                    $request->merge(['images' => array_merge($request->input('images', []), [$file])]);
                } elseif (in_array($file->getClientMimeType(), ['video/mp4', 'video/mkv', 'video/avi'])) {
                    $request->merge(['video' => $file]);
                } elseif (in_array($file->getClientMimeType(), ['audio/mp3', 'audio/wav', 'audio/aac'])) {
                    $request->merge(['audio' => $file]);
                }
            }
        }

        // Adjust PHP ini settings for file uploads
        ini_set('upload_max_filesize', '1024M');  // 1GB for individual file
        ini_set('post_max_size', '5120M');        // 2GB for total post data (all files combined)
        ini_set('memory_limit', '2560M');         // 2.5GB to handle memory operations for larger files
        ini_set('max_execution_time', '600');     // Extend script execution time to 10 minutes

        \DB::beginTransaction();
        try {
            // Validate request
            $validator = validator($request->all(), [
                'complaint_category_id' => 'required|exists:complaint_categories,id',
                'area' => 'required|string',
                'description' => 'nullable|string',

                // Updated Files validation
                'images.*' => 'nullable|mimes:jpeg,jpg,png|max:5120',  // max 2MB per image
                'video' => 'nullable|mimes:mp4,mkv,avi|max:512000',   // max 200MB for video
                'audio' => 'nullable|mimes:mp3,wav,aac|max:10240',  // max 10MB for audio
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Extract necessary data
            $area = $request->input('area');
            $complaint_category_id = $request->input('complaint_category_id');
            $description = $request->input('description');
            $complaint_by = auth()->id();

            // Get block_id and society_id of the authenticated user
            $member = User::with('member')->where('id', $complaint_by)->first()->member;
            $block_id = $member->block_id;
            $unit_type = $member->unit_type;
            $floor_number = $member->floor_number;
            $aprt_no = $member->aprt_no;
            $society_id = auth()->user()->society_id;

            $blockInfo = Block::find($block_id);
            $block_name = $blockInfo->name;
            // Get current date and time in Asia/Kolkata timezone
            $complaint_at = Carbon::now('Asia/Kolkata')->toDateTimeString();

            // Generate a 4-digit OTP
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);


            // Create complaint data
            $complaint = Complaint::create([
                'complaint_category_id' => $complaint_category_id,
                'complaint_by' => $complaint_by,
                'society_id' => $society_id,
                'block_id' => $block_id,
                'block_name' => $block_name,
                'floor_number' => $floor_number,
                'unit_type' => $unit_type,
                'aprt_no' => $aprt_no,
                'area' => $area,
                'status' => 'requested',
                'otp' => $otp,
                'description' => $description,
                'complaint_at' => $complaint_at,
            ]);

            // Handle file uploads
            $files = [];

            // Handle images (max 5, each max 20MB)
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                if (count($images) > 5) {
                    return res(
                        status: false,
                        message: 'You can only upload a maximum of 5 images',
                        code: HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                foreach ($images as $image) {
                    $path = $image->store('complaints/images', 'public');
                    $files[] = [
                        'complaint_id' => $complaint->id,
                        'path' => $path,
                        'file_type' => 'image',
                    ];
                }
            }

            // Handle video (max 1GB)
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $path = $video->store('complaints/videos', 'public');
                $files[] = [
                    'complaint_id' => $complaint->id,
                    'path' => $path,
                    'file_type' => 'video',
                ];
            }

            // Handle audio (max 500MB)
            if ($request->hasFile('audio')) {
                $audio = $request->file('audio');
                $path = $audio->store('complaints/audios', 'public');
                $files[] = [
                    'complaint_id' => $complaint->id,
                    'path' => $path,
                    'file_type' => 'audio',
                ];
            }

            // Store files in the database
            if (!empty($files)) {
                ComplaintFile::insert($files);
            }
            $complaint = Complaint::with('complaintBy', 'society')->find($complaint->id);
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // Send notifications to Admin (Panel)
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'complaints.view';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($complaint) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $complaint->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);

            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                $checkSettings = 'complaint_related_notifications';
                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'New  Complaint Received',
                            'body' => $complaint->complaintBy->name . " has lodged a complaint",
                            'model' => 'Complaint',
                            'model_id' => $complaint->id,
                            'society_name' => $complaint->society->name,
                            'society_id' => $complaint->society_id
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // to resident
            $checkForUser = $complaint_by;
            $checkForDevice = 'app';
            $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
            if ($isSettingEnabled) {
                $residentUserInfo = User::find($complaint_by);
                $deviceId = $residentUserInfo->device_id;

                $notificationMessageArray = [
                    'title' => 'Complaint Submitted Successfully',
                    'body' => 'Complaint successfully sent to the admin. Please wait till it gets assigned to a staff',
                ];

                \Log::info("<to resident - message >:::");
                sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            // =========================================================
            // send push notification to related complaint category staff service providers of this society
            // $satff_service_providers = User::select('id', 'device_id')
            //     ->whereHas('staff', function ($query) use ($complaint) {
            //         $query->where('role', 'staff')
            //             ->where('status', 'active')
            //             ->where('society_id', $complaint->society_id)
            //             ->where('complaint_category_id', $complaint->complaint_category_id);
            //     })
            //     ->get();
            // foreach ($satff_service_providers as $key => $serviceProviderUser) {
            // }
            // User::whereHas('staff', function ($query) use ($complaint) {
            //     $query->where('role', 'staff')
            //         ->where('status', 'active')
            //         ->where('society_id', $complaint->society_id)
            //         ->where('complaint_category_id', $complaint->complaint_category_id);
            // })
            //     ->select('id', 'device_id') // Select only id and device_id
            //     ->chunk(200, function ($staffUsers) use ($complaint) {
            //         foreach ($staffUsers as $notifyUser) {
            //             if ($notifyUser->device_id) {
            //                 $deviceId = $notifyUser->device_id;
            //                 $notificationMessageArray = [
            //                     'title' => 'New Service Request',
            //                     'body' => "",
            //                 ];

            //                 // Dispatch the job to send the push notification asynchronously
            //                 SendPushNotificationJob::dispatch($deviceId, $notificationMessageArray);
            //             }
            //         }
            //     });


            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            \DB::commit();

            return res(
                status: true,
                message: "Created successfully",
                data: $complaint,
                code: HTTP_CREATED
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

    public function getResidentComplaints($id = null)
    {
        try {
            $logger = auth()->user();
            $society_id = $logger->society_id;
            $user_id = $logger->id;
            // Start building the query
            // $query = Complaint::with(
            //     [
            //         'assignedTo' => function ($query) {
            //             $query->select('id', 'name', 'phone'); // Only select 'id', 'name', and 'phone'
            //         }
            //     ]
            // );
            $query = Complaint::with(
                [
                    'assignedTo' => function ($query) {
                        $query->with([
                            'staff' => function ($query) {
                                $query->with('staffCategory'); // Load staffCategory inside staff
                            }
                        ]);
                    },
                    'complaintBy' => function ($query) {
                        $query->with(['member']);
                    },
                ]
            );
            if ($id !== null) {

                $query = $query
                    ->searchByCategoryId($id);
            }
            $query = $query
                ->where('status', '!=', 'cancelled')
                ->searchBySocietyId($society_id)
                ->searchByResident($user_id)
                ->OrderBy('complaint_at', 'desc');

            $complaints = $query->paginate(25);

            // Transform response to use 'assigned_to' key
            // $complaints->getCollection()->transform(function ($complaint) {
            //     // Combine 'assignedTo' into 'assigned_to' and keep only 'name' and 'phone'
            //     if ($complaint->assignedTo) {
            //         $complaint->assigned_to = [
            //             'name' => $complaint->assignedTo->name,
            //             'phone' => $complaint->assignedTo->phone,
            //         ];
            //     } else {
            //         $complaint->assigned_to = [
            //             'name' => null,
            //             'phone' => null,
            //         ];
            //     }

            //     // Remove the 'assignedTo' key if it exists to avoid duplication
            //     unset($complaint->assignedTo);

            //     return $complaint;
            // });

            // Check if event are found
            if ($complaints->isEmpty()) {
                return res(
                    status: false,
                    message: "No complaints found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'complaints' => $complaints,
            ];
            return res(
                status: true,
                message: "Complaints retrieved successfully",
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

    public function statusCancelUpdate(Request $request)
    {
        // static cancel for resident

        \DB::beginTransaction();
        try {
            // Validate the request data
            $validator = validator($request->all(), [
                // 'status' => 'required|in:resolved,cancelled', // Only allow 'resolved' or 'cancelled' statuses
                'complaint_id' => 'required',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $complaint_id = $request->input('complaint_id');
            // Find the complaint by ID
            $complaint = Complaint::find($complaint_id);

            // Check if the complaint exists
            if (!$complaint) {
                return res(
                    status: false,
                    message: "Complaint not found!",
                    code: HTTP_OK
                );
            }

            // Check if 'assigned_to' is not null or empty and the status is not 'cancelled'
            // if (!$complaint->assigned_to) {
            //     return res(
            //         status: false,
            //         message: "Cannot update status: complaint not assigned.",
            //         code: HTTP_FORBIDDEN
            //     );
            // }

            if ($complaint->status == 'cancelled') {
                return res(
                    status: true,
                    message: "Complaint already cancelled.",
                    code: HTTP_OK
                );
            }

            // If all checks pass, update the status
            // Update status and resolved_or_cancel_on timestamp
            $complaint->status = 'cancelled';//$request->input('status');
            $complaint->resolved_or_cancelled_at = Carbon::now('Asia/Kolkata');
            $complaint->save();

            \DB::commit();

            return res(
                status: true,
                message: "Complaint cancelled successfully.",
                data: $complaint,
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

