<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\CallbackRequest;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{

    public function getStaffCategories()
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
                'staff_categories' => $complaint_categories,
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
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'complaint_category_id' => 'required|exists:complaint_categories,id',
                'name' => 'required|string',
                'phone' => 'required|string|min:10|max:10',
                'email' => 'required|email',
                'address' => 'required|string|max:255',
                'card_type' => 'required|string|max:255',
                'card_number' => 'required|string|max:255',
                'society_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = 'staff';
            $user->status = 'active';
            $user->password = Hash::make($request->input('password'));
            $user->save();


            $service_provider = new Staff();
            $service_provider->name = $request->input('name');
            $service_provider->phone = $request->input('phone');
            $service_provider->email = $request->input('email');
            $service_provider->address = $request->input('address');
            $service_provider->card_type = $request->input('card_type');
            $service_provider->card_number = $request->input('card_number');
            $service_provider->status = 'active';
            $service_provider->society_id = $request->input('society_id');
            $service_provider->complaint_category_id = $request->input('complaint_category_id');
            $service_provider->user_id = $user->id;
            $service_provider->save();


            \DB::commit();
            $data = [
                'staff' => $service_provider,
            ];

            return res(
                status: true,
                message: "Created successfully",
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

    public function getStaff(Request $request)
    {
        try {

            $validator = validator($request->all(), [
                'complaint_category_id' => 'sometimes|string|nullable',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // $user_id = auth()->id();
            $society_id = auth()->user()->society_id;

            // Start building the query
            $query = Staff::with([
                'user' => function ($query) {
                    $query->select('id', 'image'); // Only select 'id' and 'image' columns
                },
                'staffCategory'
            ])
                ->searchBySocietyId($society_id)
                ->searchByStatus('active');

            if (isset($request->complaint_category_id)) {
                $query = $query->searchByCategory($request->complaint_category_id);
            }

            // Get paginated staff
            $staff = $query->paginate(25);

            // Add image key to each staff
            $staff->getCollection()->transform(function ($staffMember) {
                $staffMember->image = $staffMember->user->image ?? null; // Assign the user's image or null if not set
                if ($staffMember->image != null) {
                    $staffMember->image = url('storage/' . $staffMember->image); // Add the URL to the image path
                }
                unset($staffMember->user); // Optionally remove the user relation if you don't need it
                return $staffMember;
            });

            // Check if result are found
            if ($staff->isEmpty()) {
                return res(
                    status: false,
                    message: "No staff found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'staffs' => $staff,
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

    public function getServiceRequest(Request $request)
    {
        try {
            $staff_user_id = auth()->id();
            $society_id = auth()->user()->society_id;

            // Start building the query
            $query = Complaint::with([
                'serviceCategory',
                'member' => function ($query) {
                    $query->select(
                        'members.user_id',
                        'members.name',
                        'members.aprt_no',
                        'members.floor_number',
                        'members.unit_type',
                        'members.phone',
                        'blocks.name as block_name'
                    )->join('blocks', 'members.block_id', '=', 'blocks.id');
                }
            ])
                ->searchBySocietyId($society_id)
                ->searchByAssignedTo($staff_user_id)
                ->searchByStatus('assigned');

            $query = $query->orderBy('assigned_at', 'desc');
            $service_requests = $query->paginate(25);

            $query2 = Complaint::with([
                'serviceCategory',
                'member' => function ($query) {
                    $query->select(
                        'members.user_id',
                        'members.name',
                        'members.aprt_no',
                        'members.floor_number',
                        'members.unit_type',
                        'members.phone',
                        'blocks.name as block_name'
                    )->join('blocks', 'members.block_id', '=', 'blocks.id');
                }
            ])
                ->searchBySocietyId($society_id)
                ->searchByAssignedTo($staff_user_id)
                ->searchByStatus('in_progress');

            $query2 = $query2->orderBy('start_at', 'desc');
            $service_running = $query2->paginate(25);

            // Check if result are found
            // if ($service_requests->isEmpty()) {
            //     return res(
            //         status: false,
            //         message: "No service request found!",
            //         code: HTTP_OK
            //     );
            // }

            // Return the response in JSON format
            $data = [
                'service_requests' => $service_requests,
                'service_running' => $service_running,
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

    public function getServiceRequestDetails(Request $request)
    {
        try {
            // Validate input
            $validator = validator($request->all(), [
                'service_request_id' => 'required|exists:complaints,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $staff_user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $service_request_id = $request->service_request_id;

            // Start building the query
            $query = Complaint::with([
                'serviceCategory',
                'complaintFiles',
                'complaintBy',
                'member' => function ($query) {
                    $query->select(
                        'members.user_id',
                        'members.name',
                        'members.aprt_no',
                        'members.floor_number',
                        'members.unit_type',
                        'members.phone',
                        'blocks.name as block_name'
                    )->join('blocks', 'members.block_id', '=', 'blocks.id');
                }
            ])
                ->searchById($service_request_id)
                ->searchBySocietyId($society_id)
                ->searchByAssignedTo($staff_user_id);
            // ->searchByStatus('assigned');

            // $query = $query->orderBy('assigned_at', 'desc');
            $service_request = $query->get();

            // Check if result are found
            if (!$service_request) {
                return res(
                    status: false,
                    message: "No service request found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'service_request' => $service_request,
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

    public function startService(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate the request data
            $validator = validator($request->all(), [
                'service_request_id' => 'required|exists:complaints,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $service_request_id = $request->service_request_id;
            // Find the complaint by ID
            $service = Complaint::searchByStatus('assigned')->find($service_request_id);

            // Check if the complaint exists
            if (!$service) {
                return res(
                    status: false,
                    message: "No pending service request found!",
                    code: HTTP_OK
                );
            }

            $service->status = 'in_progress';
            $service->start_at = Carbon::now('Asia/Kolkata');
            $service->save();

            $service = Complaint::with('assignedTo')->find($service->id);

            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            //inform notification to related resident
            $checkSett = 'complaint_related_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $service) {
                $query->where('name', $checkSett)
                    ->where('user_id', $service->complaint_by)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $service->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;

                $notificationMessageArray = [
                    'title' => 'Service Started',
                    'body' => $service->assignedTo->name . ' has started working on your complaint.',
                ];


                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            \DB::commit();

            return res(
                status: true,
                message: "Service started successfully.",
                data: $service,
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

    public function completeService(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate the request data
            $validator = validator($request->all(), [
                'service_request_id' => 'required|exists:complaints,id',
                'otp' => 'required|string',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $service_request_id = $request->service_request_id;
            // Find the complaint by ID
            $service = Complaint::searchByStatus('in_progress')->find($service_request_id);

            // Check if the complaint exists
            if (!$service) {
                return res(
                    status: false,
                    message: "No service request found!",
                    code: HTTP_NOT_FOUND
                );
            }

            if ($service->otp != $request->otp) {
                return res(
                    status: false,
                    message: "Invalid otp!",
                    code: HTTP_BAD_REQUEST
                );
            }

            $service->status = 'done';
            $service->resolved_or_cancelled_at = Carbon::now('Asia/Kolkata');
            $service->save();

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            //inform notification to related resident
            $checkSett = 'complaint_related_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $service) {
                $query->where('name', $checkSett)
                    ->where('user_id', $service->complaint_by)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $service->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;
                $notificationMessageArray = [
                    'title' => 'Complaint Resolved',
                    'body' => 'Your complaint has been successfully resolved. Thank you for your patience!',
                ];

                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            \DB::commit();

            return res(
                status: true,
                message: "Service completed successfully.",
                data: $service,
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

    public function getServiceHistory(Request $request)
    {
        try {
            // Validate the request data
            $validator = validator($request->all(), [
                'status' => 'sometimes|string|in:pending,completed,all',
                'from_date' => 'sometimes|date',
                'to_date' => 'sometimes|date',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($request->filled('from_date') && !$request->filled('to_date')) {
                    $validator->errors()->add('to_date', 'The to_date field is required when from_date is present.');
                }

                if ($request->filled('to_date') && !$request->filled('from_date')) {
                    $validator->errors()->add('from_date', 'The from_date field is required when to_date is present.');
                }
            });

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $staff_user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $status = $request->input('status', 'all');

            // Start building the query
            $query = Complaint::with([
                'serviceCategory',
                'member' => function ($query) {
                    $query->select(
                        'members.user_id',
                        'members.name',
                        'members.aprt_no',
                        'members.floor_number',
                        'members.unit_type',
                        'members.phone',
                        'blocks.name as block_name'
                    )->join('blocks', 'members.block_id', '=', 'blocks.id');
                }
            ])
                ->searchBySocietyId($society_id)
                ->searchByAssignedTo($staff_user_id);

            // Date filtering based on specified date column
            // $date_column = 'updated_at';//$request->input('date_column', 'created_at'); // Default to 'created_at' if no column is specified

            $from_date = $request->input('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : null;
            $to_date = $request->input('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : null;

            // status filtering
            $search_status = [];
            if ($status == 'all') {
                $search_status = ['assigned', 'in_progress', 'done', 'cancelled'];
                $date_column = 'assigned_at';
            } elseif ($status == 'pending') {
                $search_status = ['assigned', 'in_progress'];
                $date_column = 'start_at';
            } elseif ($status == 'completed') {
                $search_status = ['done'];
                $date_column = 'resolved_or_cancelled_at';
            }

            // Apply filters
            $query->whereIn('status', $search_status);

            if ($from_date) {
                $query->where($date_column, '>=', $from_date);
            }
            if ($to_date) {
                $query->where($date_column, '<=', $to_date);
            }

            \Log::info('Generated Query:', [
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);


            $query = $query->orderBy('created_at', 'desc');
            $service_requests = $query->paginate(25);

            // Check if result are found
            if ($service_requests->isEmpty()) {
                return res(
                    status: false,
                    message: "No service request found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'service_requests' => $service_requests,
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

}
