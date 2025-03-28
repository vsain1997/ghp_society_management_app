<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\ParcelComplaint;
use App\Models\CheckinDetail;
use App\Models\User;
use App\Notifications\DynamicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParcelController extends Controller
{
    public function elements()
    {
        try {

            $parcelTypes = [
                ["name" => "Documents"],
                ["name" => "Electronics"],
                ["name" => "Clothing"],
                ["name" => "Food"],
                ["name" => "Fragile"],
                ["name" => "Furniture"],
                ["name" => "Medicine"],
                ["name" => "Other"],
            ];

            $data = [
                'parcel_types' => $parcelTypes,
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
        $now = Carbon::now('Asia/Kolkata');
        $currentDateTime = $now->format('Y-m-d H:i:s');

        \DB::beginTransaction();
        try {
            // Validate input
            // Additional validation rules for staff_security_guard role
            $additionalRules = [];
            if (auth()->user()->role === 'staff_security_guard') {
                $additionalRules = [
                    'parcel_of' => 'required|exists:users,id',
                    'delivery_option' => 'required|in:Security Guard,Resident',
                ];
            }

            // both rule
            $validator = validator($request->all(), array_merge([
                'parcelid' => 'required|string|max:255',
                'parcel_name' => 'required|string|max:255',
                'no_of_parcel' => 'required|integer|min:1',
                'parcel_type' => 'required|string|max:255',
                'date' => 'required|date|after_or_equal:' . now()->toDateString(),
                'time' => 'required|date_format:H:i:s',
                'delivery_name' => 'nullable|string|max:255',
                'delivery_phone' => 'nullable|string|max:15',
                'parcel_company_name' => 'nullable|string',
                'delivery_agent_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ], $additionalRules));

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Check if the authenticated user can create a parcel
            $userRole = auth()->user()->role;
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('delivery_agent_image')) {
                $imagePath = $request->file('delivery_agent_image')->store('delivery_agent_image', 'public');
            }

            if (auth()->user()->role === 'staff_security_guard') {
                $parcel_of = $request->parcel_of;
                $delivery_option = $request->delivery_option;

                if ($delivery_option == 'Security Guard') {
                    // only receive

                    $insertData = [
                        'parcelid' => $request->parcelid,
                        'parcel_name' => $request->parcel_name,
                        'no_of_parcel' => $request->no_of_parcel,
                        'parcel_type' => $request->parcel_type,
                        'date' => $request->date,
                        'time' => $request->time,
                        'parcel_company_name' => $request->parcel_company_name ?? null,
                        'delivery_agent_image' => $imagePath,
                        'delivery_name' => $request->delivery_name,
                        'delivery_phone' => $request->delivery_phone,
                        'entry_by' => auth()->id(),
                        'entry_by_role' => $userRole,
                        'entry_at' => $currentDateTime,
                        'society_id' => auth()->user()->society_id,

                        'parcel_of' => $parcel_of,
                        'delivery_option' => $delivery_option,

                        'handover_status' => 'received',
                        'received_by_role' => $userRole,
                        'received_by' => auth()->id(),
                        'received_at' => $currentDateTime,
                    ];

                    // Create the parcel
                    $parcel = Parcel::create($insertData);

                    // Create checkin details
                    $checkin = CheckinDetail::create([
                        'parcel_id' => $parcel->id,
                        'status' => 'checked_out',
                        'checkin_at' => $currentDateTime,
                        'checkout_at' => $currentDateTime,
                        'checkin_by' => auth()->id(),//security guard
                        'checkout_by' => auth()->id(),//security guard
                        'visitor_of' => $parcel->parcel_of,//parcel_of
                        'society_id' => auth()->user()->society_id,
                    ]);

                    // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                    // to resident
                    $checkSettings = 'parcel_notifications';
                    $checkForUser = $checkin->visitor_of;
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $residentUserInfo = User::find($checkin->visitor_of);
                        $guardUserInfo = User::find($parcel->entry_by);//as it is guard side entry
                        $deviceId = $residentUserInfo->device_id;

                        $notificationMessageArray = [
                            'title' => 'Parcel Received',
                            'body' => 'Your parcel (' . $parcel->parcelid . ') has been received by the security guard (' . $guardUserInfo->name . '). Please collect it.',

                        ];

                        \Log::info("<to resident - message >:::");
                        sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
                    }
                    // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

                } elseif ($delivery_option == 'Resident') {
                    //when delivery agent want to deliver item to resident only,add a  checkin record
                    $insertData = [
                        'parcelid' => $request->parcelid,
                        'parcel_name' => $request->parcel_name,
                        'no_of_parcel' => $request->no_of_parcel,
                        'parcel_type' => $request->parcel_type,
                        'date' => $request->date,
                        'time' => $request->time,
                        'parcel_company_name' => $request->parcel_company_name ?? null,
                        'delivery_agent_image' => $imagePath,
                        'delivery_name' => $request->delivery_name,
                        'delivery_phone' => $request->delivery_phone,
                        'entry_by' => auth()->id(),
                        'entry_by_role' => $userRole,
                        'entry_at' => $currentDateTime,
                        'society_id' => auth()->user()->society_id,

                        'parcel_of' => $parcel_of,
                        'delivery_option' => $delivery_option,
                    ];

                    // Create the parcel
                    $parcel = Parcel::create($insertData);

                    $checkin = CheckinDetail::create([
                        'parcel_id' => $parcel->id,
                        'status' => 'checked_in',
                        'checkin_at' => $currentDateTime,
                        'checkin_by' => auth()->id(),//security guard
                        'visitor_of' => $parcel->parcel_of,//parcel_of
                        'society_id' => auth()->user()->society_id,
                    ]);

                    // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                    // to resident
                    $checkSettings = 'parcel_notifications';
                    $checkForUser = $checkin->visitor_of;
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $residentUserInfo = User::find($checkin->visitor_of);
                        $deviceId = $residentUserInfo->device_id;

                        $notificationMessageArray = [
                            'title' => 'Delivery Agent Checked In',
                            'body' => 'The delivery agent has checked in to deliver your parcel (' . $parcel->parcelid . ')',

                        ];

                        \Log::info("<to resident - message >:::");
                        sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
                    }
                    // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
                }

            } else {
                $parcel_of = auth()->id();
                $delivery_option = 'Both';

                $insertData = [
                    'parcelid' => $request->parcelid,
                    'parcel_name' => $request->parcel_name,
                    'no_of_parcel' => $request->no_of_parcel,
                    'parcel_type' => $request->parcel_type,
                    'date' => $request->date,
                    'time' => $request->time,
                    'parcel_company_name' => $request->parcel_company_name ?? null,
                    'delivery_agent_image' => $imagePath,
                    // 'delivery_name' => $request->delivery_name,
                    // 'delivery_phone' => $request->delivery_phone,
                    'entry_by' => auth()->id(),
                    'entry_by_role' => $userRole,
                    'entry_at' => $currentDateTime,
                    'society_id' => auth()->user()->society_id,

                    'parcel_of' => $parcel_of,
                    'delivery_option' => $delivery_option,
                ];
                // Create the parcel
                $parcel = Parcel::create($insertData);
            }


            \DB::commit();

            // Fetch the created parcel with its related user
            $parcelData = Parcel::with([
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
                },
                'checkinDetail'
            ])->find($parcel->id);

            $data = [
                'parcel' => $parcelData,
            ];

            return res(
                status: true,
                message: 'Parcel created successfully.',
                data: $data,
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

    public function index(Request $request)
    {
        try {

            // both rule
            $validator = validator($request->all(), array_merge([
                'filter_type' => 'nullable|string|in:pending,received,delivered,all',
            ]));

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $now = Carbon::now('Asia/Kolkata');
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $user_role = auth()->user()->role;

            // Base query
            $query = Parcel::with([
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
                },
                'checkinDetail',
                'parcelComplaint'
            ])->where('society_id', $society_id);

            // Role-specific filtering
            if ($user_role === 'resident' || $user_role === 'admin') {
                $query->where('parcel_of', $user_id);
                //resident_delete_status is null
                $query->whereNull('resident_delete_status');//hide deleted parcels from resident only
            } elseif ($user_role === 'staff_security_guard') {
                // Filter to allow the security guard to see their own entries and those of admin and residents
                // $query->whereIn('entry_by_role', ['admin', 'resident']);
                // $query->where(function ($q) use ($user_id) {
                //     $q->where('entry_by', $user_id) // Guard's own entries
                //         ->whereIn('entry_by_role', ['admin', 'resident']); // Entries by admin and resident
                // });
                // Ensure that the security guard does not see other security guards' entries
                // $query->where('entry_by_role', '!=', 'staff_security_guard');
            }

            // Apply search filter if provided
            if ($request->filled('search')) {
                $query->where('parcel_name', 'LIKE', '%' . $request->search . '%');
            }

            if ($request->filled('filter_type')) {
                if ($request->filter_type != 'all') {
                    $query->where('handover_status', 'LIKE', '%' . $request->filter_type . '%');
                }
            }

            // Apply date range filter for past_list
            // if ($request->filled('from_date') && $request->filled('to_date')) {
            //     $query->whereBetween('date', [$request->from_date, $request->to_date]);
            // }

            // Order results and paginate
            // $parcels = $query->orderBy('date', 'asc')
            //     ->orderBy('time', 'asc')
            //     ->paginate(25);

            // pending,received,delivered,all
            // if ($request->filter_type == 'all') {

            //     $query = $query->orderBy('date', 'desc')
            //         ->orderBy('time', 'desc');

            // } elseif ($request->filter_type == 'pending') {

            //     $query = $query->orderBy('date', 'desc')
            //         ->orderBy('time', 'desc');

            // } elseif ($request->filter_type == 'received') {

            //     $query = $query->orderBy('received_at', 'desc');

            // } elseif ($request->filter_type == 'delivered') {

            //     $query = $query->orderBy('handover_at', 'desc');
            // }

            $query = $query->orderBy('date', 'desc')
                ->orderBy('time', 'desc');

            $parcels = $query->paginate(25);

            // Check if parcels are found
            if ($parcels->isEmpty()) {
                return res(
                    status: false,
                    message: "No parcels found!",
                    data: null,
                    code: HTTP_OK
                );
            }

            $data = [
                'parcels' => $parcels
            ];
            // Return response
            return res(
                status: true,
                message: "Parcels retrieved successfully.",
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

    public function details($id)
    {
        try {
            // Start building the query
            $query = Parcel::with([
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
                },
                'checkinDetail',
                'parcelComplaint'
            ])
                ->searchById($id);

            $parcel = $query->get();

            // Check if parcel are found
            if ($parcel->isEmpty()) {
                return res(
                    status: false,
                    message: "No parcel found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'parcel' => $parcel,
            ];
            return res(
                status: true,
                message: "Parcel details retrieved successfully",
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

    public function deliverParcel(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');
            $currentDateTime = $now->format('Y-m-d H:i:s');
            // Validate the status input
            $validator = validator($request->all(), [
                'parcel_id' => 'required|exists:parcels,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the parcel
            $parcel = Parcel::find($request->parcel_id);

            if ($parcel->handover_status != 'received') {
                return res(
                    status: false,
                    message: 'Parcel has not been received!',
                    code: HTTP_CONFLICT
                );
            }

            if ($parcel->handover_status === 'delivered') {
                return res(
                    status: false,
                    message: 'Parcel has already been delivered!',
                    code: HTTP_CONFLICT
                );
            }

            $parcel->handover_status = 'delivered';
            $parcel->handover_to = $parcel->parcel_of;
            $parcel->handover_at = $currentDateTime;
            $parcel->save();


            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // to resident
            $checkSettings = 'parcel_notifications';
            $checkForUser = $parcel->parcel_of;
            $checkForDevice = 'app';
            $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
            if ($isSettingEnabled) {
                $residentUserInfo = User::find($parcel->parcel_of);
                $deviceId = $residentUserInfo->device_id;

                $notificationMessageArray = [
                    'title' => 'Parcel Delivered',
                    'body' => 'Your parcel (' . $parcel->parcelid . ') has been successfully delivered to you.',
                ];

                \Log::info("<to resident - message >:::");
                sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            $parcelData = Parcel::with([
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
                },
                'checkinDetail'
            ])->find($parcel->id);

            // Set a standardized message
            $message = 'Parcel has been delivered successfully';

            return res(
                status: true,
                message: $message,
                data: ['parcel' => $parcelData],
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

    public function receiveParcel(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');
            $currentDateTime = $now->format('Y-m-d H:i:s');
            // Validate the status input
            $validator = validator($request->all(), [
                'parcel_id' => 'required|exists:parcels,id',
                'delivery_name' => 'nullable|string|max:255',
                'delivery_phone' => 'nullable|string|max:15',
                'parcel_company_name' => 'nullable|string',
                'delivery_agent_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the parcel
            $parcel = Parcel::find($request->parcel_id);

            if ($parcel->handover_status === 'delivered') {
                return res(
                    status: false,
                    message: 'Parcel has already been delivered!',
                    code: HTTP_CONFLICT
                );
            }

            if ($parcel->handover_status === 'received') {
                return res(
                    status: false,
                    message: 'Parcel has already been received!',
                    code: HTTP_CONFLICT
                );
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('delivery_agent_image')) {
                $imagePath = $request->file('delivery_agent_image')->store('delivery_agent_image', 'public');
            }

            if (auth()->user()->role == 'staff_security_guard') {

                // when parcel is received by security guard
                $parcel->delivery_option = 'Security Guard';
                if ($request->filled('parcel_company_name')) {

                    $parcel->parcel_company_name = $request->parcel_company_name ?? null;
                }
                if ($imagePath != null) {

                    $parcel->delivery_agent_image = $imagePath;
                }
                if ($request->filled('delivery_name')) {

                    $parcel->delivery_name = $request->delivery_name ?? null;
                }
                if ($request->filled('delivery_phone')) {

                    $parcel->delivery_phone = $request->delivery_phone ?? null;
                }
                $parcel->handover_status = 'received';
                $parcel->received_by = auth()->id();
                $parcel->received_at = $currentDateTime;
                $parcel->received_by_role = auth()->user()->role;
                $parcel->save();

                // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                // to resident
                $checkSettings = 'parcel_notifications';
                $checkForUser = $parcel->parcel_of;
                $checkForDevice = 'app';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {
                    $residentUserInfo = User::find($parcel->parcel_of);
                    $deviceId = $residentUserInfo->device_id;
                    $guardUserInfo = User::find($parcel->received_by);//as it is guard side received

                    $notificationMessageArray = [
                        'title' => 'Parcel Received',
                        'body' => 'Your parcel (' . $parcel->parcelid . ') has been received by the security guard (' . $guardUserInfo->name . '). Please collect it.',

                    ];

                    \Log::info("<to resident - message >:::");
                    sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
                }
                // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            } else {

                // when parcel is received by resident ownself
                $parcel->delivery_option = 'Resident';
                if ($request->filled('parcel_company_name')) {

                    $parcel->parcel_company_name = $request->parcel_company_name ?? null;
                }

                if ($imagePath != null) {

                    $parcel->delivery_agent_image = $imagePath;
                }
                if ($request->filled('delivery_name')) {

                    $parcel->delivery_name = $request->delivery_name ?? null;
                }
                if ($request->filled('delivery_phone')) {

                    $parcel->delivery_phone = $request->delivery_phone ?? null;
                }

                $parcel->received_by = auth()->id();
                $parcel->received_at = $currentDateTime;
                $parcel->received_by_role = auth()->user()->role;
                $parcel->save();

                $parcel->handover_status = 'delivered';
                $parcel->handover_to = $parcel->parcel_of;
                $parcel->handover_at = $currentDateTime;
                $parcel->save();

                // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                // to resident
                $checkSettings = 'parcel_notifications';
                $checkForUser = $parcel->parcel_of;
                $checkForDevice = 'app';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {
                    $residentUserInfo = User::find($parcel->parcel_of);
                    $deviceId = $residentUserInfo->device_id;

                    $notificationMessageArray = [
                        'title' => 'Parcel Delivered',
                        'body' => 'Your parcel (' . $parcel->parcelid . ') has been successfully delivered to you.',
                    ];

                    \Log::info("<to resident - message >:::");
                    sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
                }
                // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            }

            // check-in-out at same time
            $checkin = CheckinDetail::create([
                'parcel_id' => $parcel->id,
                'status' => 'checked_out',
                'checkin_at' => $currentDateTime,
                'checkout_at' => $currentDateTime,
                'checkin_by' => auth()->id(),//security guard
                'checkout_by' => auth()->id(),//security guard
                'visitor_of' => $parcel->parcel_of,//parcel_of
                'society_id' => auth()->user()->society_id,
            ]);

            $parcelData = Parcel::with([
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
                },
                'checkinDetail'
            ])->find($parcel->id);
            // Set a standardized message
            $message = 'Parcel has been received successfully';

            return res(
                status: true,
                message: $message,
                data: ['parcel' => $parcelData],
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

    public function checkoutDeliveryAgent(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');
            $currentDateTime = $now->format('Y-m-d H:i:s');
            // Validate the status input
            $validator = validator($request->all(), [
                'parcel_id' => 'required|exists:parcels,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the parcel
            $parcel = Parcel::with('checkinDetail', 'parcelOf')->find($request->parcel_id);

            if (empty($parcel->checkinDetail->id)) {
                return res(
                    status: false,
                    message: "No Checked-in record found",
                    code: HTTP_NOT_FOUND
                );
            }

            $checkinDetailInfo = CheckinDetail::find($parcel->checkinDetail->id);
            $checkinDetailInfo->status = 'checked_out';
            $checkinDetailInfo->checkout_at = $currentDateTime;
            $checkinDetailInfo->checkout_by = auth()->id();
            $checkinDetailInfo->save();

            $parcel->received_by = $parcel->parcel_of;
            $parcel->received_at = $currentDateTime;
            $parcel->received_by_role = $parcel->parcelOf->role;

            $parcel->handover_status = 'delivered';
            $parcel->handover_to = $parcel->parcel_of;
            $parcel->handover_at = $currentDateTime;
            $parcel->save();

            //get data after update
            // $parcel = Parcel::with('checkinDetail')->find($request->parcel_id);

            $parcelData = Parcel::with([
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
                },
                'checkinDetail'
            ])->find($parcel->id);

            // Set a standardized message
            $message = 'Delivery Agent checked out successfully';

            return res(
                status: true,
                message: $message,
                data: ['parcel' => $parcelData],
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

    public function destroy($id)
    {
        \DB::beginTransaction();
        try {
            // Check if the parcel exists
            $parcel = Parcel::find($id);

            if (!$parcel) {
                return res(
                    status: false,
                    message: "Parcel not found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Check user permissions (optional, based on role)
            if (!in_array(auth()->user()->role, ['admin', 'resident']) && $parcel->society_id !== auth()->user()->society_id) {
                return res(
                    status: false,
                    message: "You do not have permission to delete this parcel",
                    code: HTTP_FORBIDDEN
                );
            }

            // here make hide from resident but still show it on guard side.
            // if fully delete then check in detail also be deleted.
            $parcel->resident_delete_status = 'deleted';
            $parcel->save();

            \DB::commit();

            return res(
                status: true,
                message: "Parcel deleted successfully",
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

    public function createComplaint(Request $request)
    {
        try {

            $now = Carbon::now('Asia/Kolkata');
            $currentDate = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');
            // Validate the input
            $validator = validator($request->all(), [
                'parcel_id' => 'required|exists:parcels,id',
                'description' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $parcel = Parcel::find($request->parcel_id);
            if (!$parcel) {
                return res(
                    status: false,
                    message: 'Parcel not found',
                    code: HTTP_NOT_FOUND
                );
            }
            // check own parcel of logger
            if ($parcel->parcel_of != auth()->id()) {
                return res(
                    status: false,
                    message: 'You are not allowed to create complaint for this parcel',
                    code: HTTP_FORBIDDEN
                );
            }

            // check own parcel of logger
            if ($parcel->handover_status == 'delivered') {
                return res(
                    status: false,
                    message: 'Order is already delivered to you',
                    code: HTTP_FORBIDDEN
                );
            }
            // Create complaint
            $existingComplaint = ParcelComplaint::where('parcel_id', $request->parcel_id)
                ->where('society_id', auth()->user()->society_id)
                ->first();
            if ($existingComplaint) {
                return res(
                    status: false,
                    message: 'Complaint already exists for this parcel',
                    code: HTTP_CONFLICT
                );
            }
            $complaint = ParcelComplaint::create([
                'parcel_id' => $request->parcel_id,
                'date' => $currentDate,
                'time' => $currentTime,
                'description' => $request->description,
                'complain_of' => auth()->id(),
                'society_id' => auth()->user()->society_id,
            ]);

            // ================================================
            // Send notifications to Admin (Panel)
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'parcel.';
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

                $checkSettings = 'parcel_complaint_notifications';
                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'New Parcel Complaint',
                            'body' => "A complaint has been raised regarding a parcel. Please check the details and take necessary action.",
                            'model' => 'Parcel',
                            'model_id' => $parcel->id,
                            'society_name' => $parcel->society->name,
                            'society_id' => $parcel->society_id,
                            'parcel_complaint' => true,
                            'parcel_complaint_model' => 'ParcelComplaint',
                            'parcel_complaint_id' => $complaint->id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================

            return res(
                status: true,
                message: 'Complaint submitted successfully',
                data: $complaint,
                code: HTTP_CREATED
            );
        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function pendingParcelCount()
    {
        try {
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $user_role = auth()->user()->role;

            // Base query
            $query = Parcel::where('society_id', $society_id)
                ->where('handover_status', 'pending');

            // Role-specific filtering
            if ($user_role === 'resident' || $user_role === 'admin') {
                $query->where('parcel_of', $user_id)
                    ->whereNull('resident_delete_status'); // Hide deleted parcels from residents
            } elseif ($user_role === 'staff_security_guard') {
                // Security guard role-specific filtering (if needed)
            }

            // Get the count
            $pendingCount = $query->count();

            return res(
                status: true,
                message: "Pending parcel count retrieved successfully.",
                data: ['pending_count' => $pendingCount],
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


}
