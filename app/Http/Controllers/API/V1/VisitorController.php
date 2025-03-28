<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VisitingFrequency;
use App\Models\Visitor;
use App\Models\VisitorBulk;
use App\Models\CheckinDetail;
use App\Models\VisitorFeedback;
use App\Models\VisitorType;
use App\Models\VisitorValidity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class VisitorController extends Controller
{
    public function elements()
    {
        try {
            $visitorTypes = VisitorType::all();
            $visitingFrequencies = VisitingFrequency::all();
            $visitorValidity = VisitorValidity::all();

            // Check if not found
            if ($visitorTypes->isEmpty() || $visitingFrequencies->isEmpty() || $visitorValidity->isEmpty()) {
                return res(
                    status: false,
                    message: "Resources are not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $data = [
                'visitor_types' => $visitorTypes,
                'visiting_frequencies' => $visitingFrequencies,
                'visitor_validity' => $visitorValidity,
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

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @notification: to resident <about:check-in> & to guard <about:ack check-in req sent>
     */
    public function store(Request $request)
    {
        $now = Carbon::now('Asia/Kolkata');
        $currentDateTime = $now->format('Y-m-d H:i:s');
        \DB::beginTransaction();
        try {
            // Decode visitors if sent as a stringified JSON
            if (is_string($request->visitors)) {
                $request->merge([
                    'visitors' => json_decode($request->visitors, true)
                ]);
            }
            // Validate input
            $validator = validator($request->all(), [
                'type_of_visitor' => 'required|string|max:255',
                'visiting_frequency' => 'required|string|max:255',
                'no_of_visitors' => 'required|integer',
                'date' => 'required|date|after_or_equal:' . now()->setTimezone('Asia/Kolkata')->toDateString(),
                'time' => 'required|date_format:H:i:s',
                'vehicle_number' => 'nullable|string|max:255',
                'purpose_of_visit' => 'required|string|max:255',
                'valid_till' => 'required|string|max:255',
                'image' => 'nullable|image|max:5120', // Optional image upload
                'visitors' => 'required|array',
                'visitors.*.name' => 'required|string|max:255',
                'visitors.*.phone' => 'required|string|size:10',
                'user_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('visitor_images', 'public');
            }

            $user_id = auth()->id();
            $visitor_classification = 'resident_related';

            if (auth()->user()->role == 'staff_security_guard') {

                if ($request->filled('user_id')) {
                    $user_id = $request->user_id;
                    if (!is_numeric($user_id)) {
                        $user_id = null; // Handle string null
                        $visitor_classification = 'other';
                    }
                } else {
                    $user_id = null; // Handle missing or empty user_id
                    $visitor_classification = 'other';
                }
            }

            // Create the main visitor
            $firstVisitor = $request->visitors[0];
            $visitor = Visitor::create([
                'type_of_visitor' => $request->type_of_visitor,
                'visiting_frequency' => $request->visiting_frequency,
                'visitor_name' => $firstVisitor['name'], // First visitor name
                'phone' => $firstVisitor['phone'], // First phone number
                'no_of_visitors' => $request->no_of_visitors,
                'date' => $request->date,
                'time' => $request->time,
                'vehicle_number' => $request->vehicle_number,
                'purpose_of_visit' => $request->purpose_of_visit,
                'valid_till' => $request->valid_till,
                'status' => 'active',
                'user_id' => $user_id,
                'added_by' => auth()->id(),
                'society_id' => auth()->user()->society_id,
                'image' => $imagePath,
                'added_by_role' => auth()->user()->role,
                'visitor_classification' => $visitor_classification,
            ]);

            // Insert remaining visitors into the visitors_bulk table
            foreach (array_slice($request->visitors, 1) as $otherVisitor) {
                VisitorBulk::create([
                    'visitor_id' => $visitor->id,
                    'name' => $otherVisitor['name'],
                    'phone' => $otherVisitor['phone'],
                ]);
            }

            if (auth()->user()->role == 'staff_security_guard') {

                if (is_numeric($visitor->user_id)) {

                    // add auto request to resident for guard added visitors incoming
                    CheckinDetail::create([
                        'visitor_id' => $visitor->id,
                        'status' => 'requested',
                        'requested_at' => $currentDateTime,
                        'request_by' => auth()->id(),
                        'visitor_of' => $visitor->user_id,
                        'society_id' => auth()->user()->society_id,
                    ]);

                    // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = $visitor->user_id;
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        // to resident
                        $now = Carbon::now('Asia/Kolkata');
                        $currentDateTime = $now->format('Y-m-d H:i:s');
                        $guardUserInfo = User::find(auth()->id());
                        $residentUserInfo = User::find($visitor->user_id);
                        $deviceId = $residentUserInfo->device_id;
                        $notificationMessageArray = [
                            'title' => 'Visitor Check-In Request',
                            'body' => "Visitor " . $visitor->visitor_name . " is here to see you. Please allow or deny their check-in.",
                            // 'bodyLocArgs' => [
                            //     $guardUserInfo->name,
                            //     $guardUserInfo->phone,
                            //     $visitor->user_id,
                            // ]
                        ];
                        $notificationDataArray = [
                            'type' => 'incoming_request',
                            'name' => $visitor->visitor_name,
                            'mob' => $visitor->phone,
                            'visitor_id' => $visitor->id,
                            'img' => $visitor->image,
                            'time' => now()->setTimezone('Asia/Kolkata')->format('h:iA')
                        ];
                        Log::info("<to resident - message >:::");
                        sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray, $notificationDataArray);
                    }
                    // to security guard
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = auth()->id();
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $guardUserInfo = User::find(auth()->id());
                        $deviceId = $guardUserInfo->device_id;
                        $notificationMessageArray = [
                            'title' => 'Check-In Request Sent',
                            'body' => 'The visitor request for ' . $visitor->visitor_name . ' has been sent to the resident for approval',
                        ];
                        Log::info("<to guard - message >:::");
                        sendAppPushNotification($guardUserInfo->id, $deviceId, $notificationMessageArray);
                    }
                    // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
                } else {

                    CheckinDetail::create([

                        'visitor_id' => $visitor->id,
                        'status' => 'checked_in',
                        'checkin_at' => $currentDateTime,
                        'checkin_by' => auth()->id(),
                        'visitor_of' => null,
                        'society_id' => auth()->user()->society_id,
                    ]);

                }
            }

            \DB::commit();

            $visitorData = Visitor::with([
                'bulkVisitors',
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
                'lastCheckinDetail' => function ($query) {
                    $query->latest('created_at'); // Get the latest check-in record based on creation time
                },
            ])->find($visitor->id);

            $data = [
                'visitor' => $visitorData,
            ];

            return res(
                status: true,
                message: "Visitor created successfully",
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
            $now = Carbon::now('Asia/Kolkata');
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $logger_role = auth()->user()->role;
            if ($logger_role == 'resident' || $logger_role == 'admin') {

                // for resident end api
                $query = Visitor::
                    // upcoming()
                    with([
                        'bulkVisitors',
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
                        'lastCheckinDetail' => function ($query) {
                            $query->latest('created_at'); // Get the latest check-in record based on creation time
                        },
                        'visitorFeedback'
                    ])
                    ->searchByResident($user_id)
                    ->where('society_id', $society_id)
                    ->where('visitor_classification', 'resident_related')
                    ->orderBy('date', 'desc')
                    ->orderBy('time', 'desc');

                if (!empty($request->search)) {
                    $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                }

                $visitors = $query->paginate(25);

            } else if (auth()->user()->role == 'staff_security_guard') {

                $validator = validator($request->all(), [
                    'filter_type' => 'required|in:resident_entry,guard_entry,daily_frequency,today_list,past_list,all',
                ]);

                if ($validator->fails()) {
                    return res(
                        status: false,
                        message: $validator->errors()->first(),
                        code: HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                /**
                 * resident pre-auth
                 * by guard
                 * frequency : daily
                 * date filter in past
                 * today list
                 */
                if ($request->filter_type == 'resident_entry') {

                    $query = Visitor::
                        with([
                            'bulkVisitors',
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
                            'lastCheckinDetail' => function ($query) {
                                $query->latest('created_at'); // Get the latest check-in record based on creation time
                            },
                            'visitorFeedback'
                        ])
                        ->whereDate('date', '=', $now->toDateString())
                        // ->where('visiting_frequency', '!=', 'Daily')
                        ->where('added_by', '!=', auth()->id())
                        ->where(function ($q) {
                            $q->where('added_by_role', '=', 'resident')
                                ->orWhere('added_by_role', '=', 'admin');
                        })
                        ->where('society_id', $society_id)
                        ->where('visitor_classification', 'resident_related')
                        ->orderBy('date', 'desc')
                        ->orderBy('time', 'desc');
                    // ->paginate(25);

                    if (!empty($request->search)) {
                        $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                    }

                    $visitors = $query->paginate(25);

                } elseif ($request->filter_type == 'guard_entry') {

                    $query = Visitor::with([
                        'bulkVisitors',
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
                        'lastCheckinDetail' => function ($query) {
                            $query->latest('created_at'); // Get the latest check-in record based on creation time
                        },
                        'visitorFeedback'
                    ])
                        ->whereDate('date', '=', $now->toDateString())
                        // ->where('visiting_frequency', '!=', 'Daily')
                        ->where('added_by_role', '=', 'staff_security_guard')
                        ->where('society_id', $society_id)
                        ->orderBy('date', 'desc')
                        ->orderBy('time', 'desc');

                    if (!empty($request->search)) {
                        $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                    }

                    $visitors = $query->paginate(25);


                } elseif ($request->filter_type == 'daily_frequency') {

                    $query = Visitor::with([
                        'bulkVisitors',
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
                        'lastCheckinDetail' => function ($query) {
                            $query->latest('created_at'); // Get the latest check-in record based on creation time
                        },
                        'visitorFeedback'
                    ])
                        ->where('visiting_frequency', '=', 'Daily')
                        ->where('society_id', $society_id)
                        ->orderBy('date', 'desc')
                        ->orderBy('time', 'desc');

                    if (!empty($request->search)) {
                        $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                    }

                    $visitors = $query->paginate(25);

                    // Process each visitor to check their last check-in detail
                    $visitors->getCollection()->transform(function ($visitor) {
                        // Get the last check-in detail for the visitor
                        if ($visitor->lastCheckinDetail) {
                            $lastCheckin = $visitor->lastCheckinDetail;
                            $checkoutAt = Carbon::parse($lastCheckin->checkout_at);

                            // Set timezone to Asia/Kolkata
                            $today = Carbon::now('Asia/Kolkata')->toDateString();

                            // Check if the status is 'checked_out' and the checkout date is not today
                            if ($lastCheckin->status === 'checked_out' && $checkoutAt->toDateString() !== $today) {
                                // Set 'last_checkin_detail' to null
                                // $visitor->last_checkin_detail = null;
                                // Completely remove the lastCheckinDetail relation
                                $visitor->setRelation('lastCheckinDetail', null);
                            }
                        }

                        return $visitor;
                    });


                } elseif ($request->filter_type == 'today_list') {

                    $query = Visitor::with([
                        'bulkVisitors',
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
                        'lastCheckinDetail' => function ($query) {
                            $query->latest('created_at'); // Get the latest check-in record based on creation time
                        },
                        'visitorFeedback'
                    ])
                        ->where(function ($q) use ($now) {
                            $q->whereDate('date', '=', $now->toDateString())
                                ->orWhere('visiting_frequency', '=', 'Daily');
                        })
                        ->where('society_id', $society_id)
                        ->orderBy('date', 'desc')
                        ->orderBy('time', 'desc');

                    if (!empty($request->search)) {
                        $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                    }

                    $visitors = $query->paginate(25);
                    $visitors->getCollection()->transform(function ($visitor) {
                        // Get the last check-in detail for the visitor
                        if ($visitor->visiting_frequency === 'Daily' && $visitor->lastCheckinDetail) {
                            $lastCheckin = $visitor->lastCheckinDetail;
                            $checkoutAt = Carbon::parse($lastCheckin->checkout_at);

                            // Set timezone to Asia/Kolkata
                            $today = Carbon::now('Asia/Kolkata')->toDateString();

                            // Check if the status is 'checked_out' and the checkout date is not today
                            if ($lastCheckin->status === 'checked_out' && $checkoutAt->toDateString() !== $today) {
                                // Set 'last_checkin_detail' to null
                                // $visitor->last_checkin_detail = null;
                                // Completely remove the lastCheckinDetail relation
                                $visitor->setRelation('lastCheckinDetail', null);
                            }
                        }

                        return $visitor;
                    });

                } elseif ($request->filter_type == 'past_list') {

                    $query = Visitor::with([
                        'bulkVisitors',
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
                        'lastCheckinDetail' => function ($query) {
                            $query->latest('created_at'); // Get the latest check-in record based on creation time
                        },
                        'visitorFeedback'
                    ])
                        ->where('society_id', $society_id)
                        ->whereDate('date', '<', $now->toDateString());

                    // Add date filter if both `from_date` and `to_date` are provided
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereBetween('date', [$request->from_date, $request->to_date]);
                    }

                    // Add search filter if the `search` parameter is provided
                    if (!empty($request->search)) {
                        $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                    }

                    // Add sorting and pagination
                    $visitors = $query->orderBy('date', 'desc')
                        ->orderBy('time', 'desc')
                        ->paginate(25);
                } elseif ($request->filter_type == 'all') {
                    $query = Visitor::with([
                        'bulkVisitors',
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
                        'lastCheckinDetail' => function ($query) {
                            $query->latest('created_at'); // Get the latest check-in record based on creation time
                        },
                        'visitorFeedback'
                    ])->where(function ($q) use ($now) {
                        $q->whereDate('date', '=', $now->toDateString())
                            ->orWhere('visiting_frequency', '=', 'Daily');
                    })
                        // ->where('visiting_frequency', '=', 'Daily')
                        ->where('society_id', $society_id)
                        ->orderBy('date', 'desc')
                        ->orderBy('time', 'desc');

                    if (!empty($request->search)) {
                        $query->where('visitor_name', 'LIKE', '%' . $request->search . '%');
                    }

                    $visitors = $query->paginate(25);

                    // Process each visitor to check their last check-in detail
                    $visitors->getCollection()->transform(function ($visitor) {
                        // Get the last check-in detail for the visitor
                        if ($visitor->visiting_frequency === 'Daily' && $visitor->lastCheckinDetail) {
                            $lastCheckin = $visitor->lastCheckinDetail;
                            $checkoutAt = Carbon::parse($lastCheckin->checkout_at);

                            // Set timezone to Asia/Kolkata
                            $today = Carbon::now('Asia/Kolkata')->toDateString();

                            // Check if the status is 'checked_out' and the checkout date is not today
                            if ($lastCheckin->status === 'checked_out' && $checkoutAt->toDateString() !== $today) {
                                // Set 'last_checkin_detail' to null
                                // $visitor->last_checkin_detail = null;
                                // Completely remove the lastCheckinDetail relation
                                $visitor->setRelation('lastCheckinDetail', null);
                            }
                        }

                        return $visitor;
                    });
                }
            }

            // get count
            $vcount = $this->visitorCount();

            $data = [
                'today_visitors_count' => $vcount->today_visitors_count,
                'past_visitors_count' => $vcount->past_visitors_count,
                'visitors' => null,
            ];

            // Check if visitors are found
            if ($visitors->isEmpty()) {
                return res(
                    status: false,
                    message: "No visitors found!",
                    data: $data,
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'today_visitors_count' => $vcount->today_visitors_count,
                'past_visitors_count' => $vcount->past_visitors_count,
                'visitors' => $visitors,
            ];
            return res(
                status: true,
                message: "Visitors retrieved successfully",
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

    public function details($id)
    {
        try {
            // Start building the query
            $query = Visitor::with([
                'bulkVisitors',
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
                'lastCheckinDetail' => function ($query) {
                    $query->latest('created_at'); // Get the latest check-in record based on creation time
                },
                'visitorFeedback'
            ])
                ->searchById($id);

            $visitor = $query->get();

            // Check if visitor are found
            if ($visitor->isEmpty()) {
                return res(
                    status: false,
                    message: "No visitor found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'visitor' => $visitor,
            ];
            return res(
                status: true,
                message: "Visitor details retrieved successfully",
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

    public function changeStatus(Request $request, $id)
    {
        try {
            // Validate the status input
            $validator = validator($request->all(), [
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the visitor
            $visitor = Visitor::find($id);

            // Update the status
            $visitor->status = $request->status;
            $visitor->save();

            // Set a standardized message
            $message = $visitor->status === 'active' ? 'Visitor has been activated successfully!' : 'Visitor has been blocked successfully!';

            return res(
                status: true,
                message: $message,
                data: ['status' => $visitor->status],
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

    public function visitorCount()
    {
        $now = Carbon::now('Asia/Kolkata');
        $user_id = auth()->id();
        $society_id = auth()->user()->society_id;

        $todayVisitorCount = Visitor::where(function ($q) use ($now) {
            $q->whereDate('date', '=', $now->toDateString())
                ->orWhere('visiting_frequency', '=', 'Daily');
        })
            ->where('society_id', $society_id)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->count();

        // past list
        $pastVisitorCount = Visitor::where('society_id', $society_id)
            ->whereDate('date', '<', $now->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->count();

        // Return the response in JSON format
        return (object) [
            'today_visitors_count' => $todayVisitorCount,
            'past_visitors_count' => $pastVisitorCount,
        ];
    }

    /**
     * Summary of checkIn
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @notification : to resident <about::checkIn-success>
     */
    public function checkIn(Request $request)
    {
        try {
            // Validate incoming data
            $validator = validator($request->all(), [
                'visitor_id' => 'required|exists:visitors,id',
                'checkin_at' => 'required|date_format:Y-m-d H:i:s',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the visitor
            $visitor = Visitor::find($request->visitor_id);

            // Check if the visitor's last check-in record is already checked in
            $lastCheckin = CheckinDetail::where('visitor_id', $request->visitor_id)
                ->latest('created_at') // Get the most recent record
                ->first();

            if (isset($lastCheckin->status) && !empty($lastCheckin->status) && $lastCheckin->status === 'checked_in') {
                return res(
                    status: false,
                    message: 'Visitor is already checked in. Re-check-in is not allowed.',
                    code: HTTP_BAD_REQUEST
                );
            }

            if ($visitor->added_by_role == 'resident' || $visitor->added_by_role == 'admin') {
                //pre-authorised visitor, direct checkin
                $checkin = CheckinDetail::create([
                    'visitor_id' => $request->visitor_id,
                    'status' => 'checked_in',
                    'checkin_at' => $request->checkin_at,
                    'checkin_by' => auth()->id(),
                    'visitor_of' => $visitor->user_id,
                    'society_id' => auth()->user()->society_id,
                ]);

            } elseif ($visitor->added_by_role == 'staff_security_guard') {
                // check is resident / admin accepted incoming permissions
                // add daily frequency check
                if ($visitor->visiting_frequency == 'Daily') {
                    // direct check in for daily
                    $checkin = CheckinDetail::create([
                        'visitor_id' => $request->visitor_id,
                        'status' => 'checked_in',
                        'checkin_at' => $request->checkin_at,
                        'checkin_by' => auth()->id(),
                        'visitor_of' => $visitor->user_id,
                        'society_id' => auth()->user()->society_id,
                    ]);
                } else {

                    $checkinDetail = CheckinDetail::where('visitor_id', $visitor->visitor_id)
                        ->where('status', 'allowed')
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($checkinDetail) {
                        //resident allowed and do checkin
                        $checkin = CheckinDetail::create([
                            'visitor_id' => $request->visitor_id,
                            'status' => 'checked_in',
                            'checkin_at' => $request->checkin_at,
                            'checkin_by' => auth()->id(),
                            'visitor_of' => $visitor->user_id,
                            'society_id' => auth()->user()->society_id,
                        ]);
                    } else {
                        return res(
                            status: false,
                            message: 'Check-in Failed ! Resident did not allowed.',
                            code: HTTP_NOT_FOUND
                        );
                    }
                }
            }

            if ($checkin->visitor_of != null) {
                // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                // to resident
                $checkSettings = 'visitor_notifications';
                $checkForUser = $checkin->visitor_of;
                $checkForDevice = 'app';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {
                    $residentUserInfo = User::find($checkin->visitor_of);
                    $deviceId = $residentUserInfo->device_id;

                    $notificationMessageArray = [
                        'title' => 'Visitor Checked In',
                        'body' => 'Your visitor ' . $visitor->visitor_name . 'has successfully checked in.',
                    ];

                    Log::info("<to resident - message >:::");
                    sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
                }
                // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            }

            return response()->json([
                'status' => true,
                'message' => 'Visitor check-in successfully.',
                'data' => $checkin,
            ], 201);

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Summary of checkOut
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @notification : to resident <about::checkOut-success>
     */
    public function checkOut(Request $request)
    {
        try {
            // Validate incoming data
            $validator = validator($request->all(), [
                'visitor_id' => 'required|exists:visitors,id',
                'checkout_at' => 'required|date_format:Y-m-d H:i:s',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $checkoutDetail = CheckinDetail::where('visitor_id', $request->visitor_id)
                ->where('status', 'checked_in')
                ->latest('created_at')
                ->first();

            if ($checkoutDetail) {
                //resident allowed and do checkin
                $checkoutDetail->status = 'checked_out';
                $checkoutDetail->checkout_at = $request->checkout_at;
                $checkoutDetail->checkout_by = auth()->id();
                $checkoutDetail->save();

                if ($checkoutDetail->visitor_of != null) {

                    // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                    // to resident
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = $checkoutDetail->visitor_of;
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $residentUserInfo = User::find($checkoutDetail->visitor_of);
                        $deviceId = $residentUserInfo->device_id;
                        $visitor = Visitor::find($request->visitor_id);

                        $notificationMessageArray = [
                            'title' => 'Visitor Checked Out',
                            'body' => 'Your visitor ' . $visitor->visitor_name . 'has successfully checked out.',
                        ];

                        Log::info("<to resident - message >:::");
                        sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
                    }
                    // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
                }
            } else {
                return res(
                    status: false,
                    message: 'Check-out Failed ! Visitor is not checked In.',
                    code: HTTP_NOT_FOUND
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Visitor check-out successfully.',
                'data' => $checkoutDetail,
            ], 201);

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function giveFeedback(Request $request)
    {
        try {
            // Validate the input
            $validator = validator($request->all(), [
                'visitor_id' => 'required|exists:visitors,id',
                'feedback' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $visitor = Visitor::find($request->visitor_id);
            if (!$visitor) {
                return res(
                    status: false,
                    message: 'Visitor not found',
                    code: HTTP_NOT_FOUND
                );
            }
            // check own visitor of logger
            if ($visitor->user_id != auth()->id()) {
                return res(
                    status: false,
                    message: 'You are not allowed to give feedback for this visitor',
                    code: HTTP_FORBIDDEN
                );
            }
            // Create feedback
            $existingFeedback = VisitorFeedback::where('visitor_id', $request->visitor_id)
                ->where('society_id', auth()->user()->society_id)
                ->first();
            if ($existingFeedback) {
                return res(
                    status: false,
                    message: 'Feedback already exists for this visitor',
                    // data: $existingFeedback,
                    code: HTTP_CONFLICT
                );
            }
            $feedback = VisitorFeedback::create([
                'visitor_id' => $request->visitor_id,
                'feedback' => $request->feedback,
                'feedback_by' => auth()->id(),
                'society_id' => auth()->user()->society_id,
            ]);

            return res(
                status: true,
                message: 'Feedback submitted successfully',
                data: $feedback,
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

    /**
     * Summary of visitorIncomingRequest
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @notification: to resident <about:check-in> & to guard <about:ack check-in req sent>
     */
    public function visitorIncomingRequest(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');
            $currentDateTime = $now->format('Y-m-d H:i:s');
            // Validate incoming data
            $validator = validator($request->all(), [
                'visitor_id' => 'required|exists:visitors,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the visitor
            $visitor = Visitor::find($request->visitor_id);

            if ($visitor && $visitor->status == 'inactive') {
                // if visitor is inactive, send error msg
                return res(
                    status: false,
                    message: 'Request cannot be made to blocked visitor',
                    code: HTTP_BAD_REQUEST
                );
            }

            // Check if the visitor's last check-in record is already checked in
            $lastCheckin = CheckinDetail::where('visitor_id', $request->visitor_id)
                ->latest('created_at') // Get the most recent record
                ->first();

            if ($lastCheckin) {
                if ($lastCheckin->status === 'checked_in') {
                    // send error msg already checked in
                    return res(
                        status: false,
                        message: 'Visitor is already checked in. Re-check-in is not allowed',
                        code: HTTP_BAD_REQUEST
                    );
                } elseif ($lastCheckin->status === 'allowed') {
                    // send error msg already allowed
                    return res(
                        status: false,
                        message: 'Visitor is already allowed to checked in',
                        code: HTTP_BAD_REQUEST
                    );
                } elseif ($lastCheckin->status === 'checked_out' || $lastCheckin->status === 'not_responded' || $lastCheckin->status === 'not_allowed') {
                    // for not_responded / not_allowed, show status
                    // create new request
                    $incommingRequest = CheckinDetail::create([
                        'visitor_id' => $request->visitor_id,
                        'status' => 'requested',
                        'requested_at' => $currentDateTime,
                        'request_by' => auth()->id(),
                        'visitor_of' => $visitor->user_id,
                        'society_id' => auth()->user()->society_id,
                    ]);


                    // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                    // to resident
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = $visitor->user_id;
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $guardUserInfo = User::find(auth()->id());
                        $residentUserInfo = User::find($visitor->user_id);
                        $deviceId = $residentUserInfo->device_id;
                        $notificationMessageArray = [
                            'title' => 'Visitor Check-In Request',
                            'body' => "Visitor " . $visitor->visitor_name . " is here to see you. Please allow or deny their check-in.",
                            // 'bodyLocArgs' => [
                            //     $guardUserInfo->name,
                            //     $guardUserInfo->phone,
                            //     $visitor->user_id,
                            // ]
                        ];
                        $notificationDataArray = [
                            'type' => 'incoming_request',
                            'name' => $visitor->visitor_name,
                            'mob' => $visitor->phone,
                            // 'name' => $guardUserInfo->name,
                            // 'mob' => $guardUserInfo->phone,
                            'visitor_id' => $visitor->id,
                            'img' => $visitor->image,
                            'time' => now()->setTimezone('Asia/Kolkata')->format('h:iA')
                        ];
                        Log::info("<to resident - message >:::");
                        sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray, $notificationDataArray);
                    }
                    // to security guard
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = auth()->id();
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $guardUserInfo = User::find(auth()->id());
                        $deviceId = $guardUserInfo->device_id;
                        $notificationMessageArray = [
                            'title' => 'Check-In Request Sent',
                            'body' => 'The visitor request for ' . $visitor->visitor_name . ' has been sent to the resident for approval',
                        ];
                        Log::info("<to guard - message >:::");
                        sendAppPushNotification($guardUserInfo->id, $deviceId, $notificationMessageArray);
                    }
                    // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

                } elseif ($lastCheckin->status === 'requested') {
                    // update last record
                    $lastCheckin->requested_at = $currentDateTime;
                    $lastCheckin->status = 'requested';
                    $lastCheckin->request_by = auth()->id();
                    $lastCheckin->save();
                    $incommingRequest = $lastCheckin;


                    // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                    // to resident
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = $visitor->user_id;
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $guardUserInfo = User::find(auth()->id());
                        $residentUserInfo = User::find($visitor->user_id);
                        $deviceId = $residentUserInfo->device_id;
                        $notificationMessageArray = [
                            'title' => 'Visitor Check-In Request',
                            'body' => "Visitor " . $visitor->visitor_name . " is here to see you. Please allow or deny their check-in.",
                            // 'bodyLocArgs' => [
                            //     $guardUserInfo->name,
                            //     $guardUserInfo->phone,
                            //     $visitor->user_id,
                            // ]
                        ];
                        $notificationDataArray = [
                            'type' => 'incoming_request',
                            'name' => $visitor->visitor_name,
                            'mob' => $visitor->phone,
                            // 'name' => $guardUserInfo->name,
                            // 'mob' => $guardUserInfo->phone,
                            'visitor_id' => $visitor->id,
                            'img' => $visitor->image,
                            'time' => now()->setTimezone('Asia/Kolkata')->format('h:iA')
                        ];
                        Log::info("<to resident - message >:::");
                        sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray, $notificationDataArray);
                    }
                    // to security guard
                    $checkSettings = 'visitor_notifications';
                    $checkForUser = auth()->id();
                    $checkForDevice = 'app';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {
                        $guardUserInfo = User::find(auth()->id());
                        $deviceId = $guardUserInfo->device_id;
                        $notificationMessageArray = [
                            'title' => 'Check-In Request Sent',
                            'body' => 'The visitor request for ' . $visitor->visitor_name . ' has been sent to the resident for approval',
                        ];
                        Log::info("<to guard - message >:::");
                        sendAppPushNotification($guardUserInfo->id, $deviceId, $notificationMessageArray);
                    }
                    // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
                }
            } else {
                // create new request
                $incommingRequest = CheckinDetail::create([
                    'visitor_id' => $request->visitor_id,
                    'status' => 'requested',
                    'requested_at' => $currentDateTime,
                    'request_by' => auth()->id(),
                    'visitor_of' => $visitor->user_id,
                    'society_id' => auth()->user()->society_id,
                ]);


                // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                // to resident
                $checkSettings = 'visitor_notifications';
                $checkForUser = $visitor->user_id;
                $checkForDevice = 'app';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {
                    $guardUserInfo = User::find(auth()->id());
                    $residentUserInfo = User::find($visitor->user_id);
                    $deviceId = $residentUserInfo->device_id;
                    $notificationMessageArray = [
                        'title' => 'Visitor Check-In Request',
                        'body' => "Visitor " . $visitor->visitor_name . " is here to see you. Please allow or deny their check-in.",
                        // 'bodyLocArgs' => [
                        //     $guardUserInfo->name,
                        //     $guardUserInfo->phone,
                        //     $visitor->user_id,
                        // ]
                    ];
                    $notificationDataArray = [
                        'type' => 'incoming_request',
                        'name' => $visitor->visitor_name,
                        'mob' => $visitor->phone,
                        // 'name' => $guardUserInfo->name,
                        // 'mob' => $guardUserInfo->phone,
                        'visitor_id' => $visitor->id,
                        'img' => $visitor->image,
                        'time' => now()->setTimezone('Asia/Kolkata')->format('h:iA')
                    ];
                    Log::info("<to resident - message >:::");
                    sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray, $notificationDataArray);
                }
                // to security guard
                $checkSettings = 'visitor_notifications';
                $checkForUser = auth()->id();
                $checkForDevice = 'app';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {
                    $guardUserInfo = User::find(auth()->id());
                    $deviceId = $guardUserInfo->device_id;
                    $notificationMessageArray = [
                        'title' => 'Check-In Request Sent',
                        'body' => 'The visitor request for ' . $visitor->visitor_name . ' has been sent to the resident for approval',
                    ];
                    Log::info("<to guard - message >:::");
                    sendAppPushNotification($guardUserInfo->id, $deviceId, $notificationMessageArray);
                }
                // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            }

            return response()->json([
                'status' => true,
                'message' => 'Visitor Incoming Request created successfully',
                'data' => $incommingRequest,
            ], 201);

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
            // Check if the visitor exists
            $visitor = Visitor::find($id);

            if (!$visitor) {
                return res(
                    status: false,
                    message: "Visitor not found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Check user permissions (optional, based on role)
            if (!in_array(auth()->user()->role, ['admin', 'resident']) && $visitor->society_id !== auth()->user()->society_id) {
                return res(
                    status: false,
                    message: "You do not have permission to delete this visitor",
                    code: HTTP_FORBIDDEN
                );
            }

            // Delete visitor's associated bulk visitors
            // VisitorBulk::where('visitor_id', $visitor->id)->delete();

            // Delete the visitor record
            $visitor->delete();
            $visitor->forceDelete();

            \DB::commit();

            return res(
                status: true,
                message: "Visitor deleted successfully",
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

    /**
     * Summary of visitorIncomingResponse
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @notification: to guard <about:allow/deny for visitor checkIn>
     */
    public function visitorIncomingResponse(Request $request)
    {
        try {
            // if status not changed to allowed,not_allowed then it means not_responded
            $now = Carbon::now('Asia/Kolkata');
            $currentDateTime = $now->format('Y-m-d H:i:s');
            // Validate incoming data
            $validator = validator($request->all(), [
                'visitor_id' => 'required|exists:visitors,id',
                'status' => 'required|in:allowed,not_allowed',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $visitor = Visitor::find($request->visitor_id);
            // Check if the visitor's last check-in record is already checked in
            $lastCheckin = CheckinDetail::where('visitor_id', $request->visitor_id)
                ->latest('created_at') // Get the most recent record
                ->first();

            if (!$lastCheckin) {
                return res(
                    status: false,
                    message: 'No request record found for this visitor',
                    code: HTTP_NOT_FOUND
                );
            }
            if ($lastCheckin->status !== 'requested') {
                return res(
                    status: false,
                    message: 'Invalid status. Only requested status can be updated',
                    code: HTTP_BAD_REQUEST
                );
            }
            if ($request->status == 'not_allowed') {

                // allow or decline
                $lastCheckin->status = $request->status;
                $lastCheckin->save();
            } elseif ($request->status == 'allowed') {

                if ($visitor->added_by_role == 'resident' || $visitor->added_by_role == 'admin') {
                    $lastCheckin->status = 'checked_in'; //as it is allowed to be check-in
                    $lastCheckin->checkin_at = $currentDateTime;
                    $lastCheckin->checkin_by = auth()->id();
                    $lastCheckin->save();
                } else {
                    $lastCheckin->status = 'checked_in'; //as it is allowed to be check-in
                    $lastCheckin->checkin_at = $currentDateTime;
                    $lastCheckin->checkin_by = $lastCheckin->request_by;
                    $lastCheckin->save();
                }
            }
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // to resident
            $checkSettings = 'visitor_notifications';
            $checkForUser = $lastCheckin->visitor_of;
            $checkForDevice = 'app';
            $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
            if ($isSettingEnabled) {
                $residentUserInfo = User::find($lastCheckin->visitor_of);
                $deviceId = $residentUserInfo->device_id;

                $notificationMessageArray = [
                    'title' => 'Visitor Checked In',
                    'body' => 'Your visitor ' . $visitor->visitor_name . ' has successfully checked in.',
                ];

                Log::info("<to resident - message >:::");
                sendAppPushNotification($residentUserInfo->id, $deviceId, $notificationMessageArray);
            }
            // to security guard
            $checkSettings = 'visitor_notifications';
            $checkForUser = $lastCheckin->request_by;
            $checkForDevice = 'app';
            $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
            if ($isSettingEnabled) {
                $guardUserInfo = User::find($lastCheckin->request_by);
                $deviceId = $guardUserInfo->device_id;
                if ($lastCheckin->status === 'allowed') {

                    $notificationMessageArray = [
                        'title' => 'Visitor Check-In Approved',
                        'body' => 'Resident has approved the check-in for ' . $visitor->visitor_name . '. Please proceed with the check-in.',
                    ];
                } elseif ($lastCheckin->status == 'not_allowed') {
                    $notificationMessageArray = [
                        'title' => 'Visitor Check-In Declined',
                        'body' => 'Resident has declined the check-in for ' . $visitor->visitor_name . '.',
                    ];
                }
                Log::info("<to guard - message >:::");
                sendAppPushNotification($guardUserInfo->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            $message = $request->status === 'allowed'
                ? 'Visitor is checked in successfully.'
                : 'Visitor is not allowed to visit.';

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $lastCheckin,
            ], 201);

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Summary of residentNotResponding
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @notification: to guard <about:resident is not-responded>
     */
    public function residentNotResponding(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');
            $currentDateTime = $now->format('Y-m-d H:i:s');
            // Validate incoming data
            $validator = validator($request->all(), [
                'visitor_id' => 'required|exists:visitors,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the visitor
            $visitor = Visitor::find($request->visitor_id);

            if ($visitor && $visitor->status == 'inactive') {
                // if visitor is inactive, send error msg
                return res(
                    status: false,
                    message: 'Request cannot be made to blocked visitor',
                    code: HTTP_BAD_REQUEST
                );
            }

            // Check if the visitor's last check-in record is already checked in
            $lastCheckin = CheckinDetail::where('visitor_id', $request->visitor_id)
                ->latest('created_at') // Get the most recent record
                ->first();

            if ($lastCheckin) {
                if ($lastCheckin->status === 'checked_in') {
                    // send error msg already checked in
                    return res(
                        status: false,
                        message: 'Visitor is already checked in. Re-check-in is not allowed',
                        code: HTTP_BAD_REQUEST
                    );
                } elseif ($lastCheckin->status === 'allowed') {
                    // send error msg already allowed
                    return res(
                        status: false,
                        message: 'Visitor is already allowed to checked in',
                        code: HTTP_BAD_REQUEST
                    );
                } elseif ($lastCheckin->status === 'requested' || $lastCheckin->status === 'not_responded' || $lastCheckin->status === 'not_allowed') {
                    // for not_responded / not_allowed, show status
                    // create new request
                    $lastCheckin->status = 'not_responded';
                    $lastCheckin->save();

                    if (auth()->user()->role != 'staff_security_guard') {

                        // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
                        $checkSettings = 'visitor_notifications';
                        $checkForUser = $lastCheckin->request_by;
                        $checkForDevice = 'app';
                        $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                        if ($isSettingEnabled) {
                            $guardUserInfo = User::find($lastCheckin->request_by);
                            $deviceId = $guardUserInfo->device_id;
                            $notificationMessageArray = [
                                'title' => 'Visitor Check-In Call Unanswered',
                                'body' => 'The resident didn\'t answer the check-in call from ' . $visitor->visitor_name . '. Please try again later',
                            ];
                            sendAppPushNotification($guardUserInfo->id, $deviceId, $notificationMessageArray);
                        }
                        // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
                    }
                }
            }

            $lastCheckin = CheckinDetail::where('visitor_id', $request->visitor_id)
                ->latest('created_at') // Get the most recent record
                ->first();

            return response()->json([
                'status' => true,
                'message' => 'Not Answered status changed successfully',
                'data' => $lastCheckin,
            ], 201);

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function visitorIncomingRequestList(Request $request)
    {
        try {
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $logger_role = auth()->user()->role;

            if ($logger_role == 'resident' || $logger_role == 'admin') {
                // Query for visitors with a related CheckinDetail where status is 'requested'
                $visitor = Visitor::with([
                    'bulkVisitors',
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
                    'lastCheckinDetail' => function ($query) {
                        $query->where('status', 'requested')->latest('created_at');
                    },
                    'visitorFeedback'
                ])
                    ->where('society_id', $society_id)
                    ->whereHas('lastCheckinDetail', function ($query) {
                        $query->where('status', 'requested');
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('time', 'desc')
                    ->first(); // Get only one visitor

                // Check if a visitor is found
                if (!$visitor) {
                    return res(
                        status: false,
                        message: "No visitor found!",
                        data: ['visitor' => null],
                        code: HTTP_OK
                    );
                }

                // Return the visitor data
                return res(
                    status: true,
                    message: "Visitor data retrieved successfully",
                    data: ['visitor' => $visitor],
                    code: HTTP_OK
                );
            }

            return res(
                status: false,
                message: "Unauthorized access!",
                code: HTTP_FORBIDDEN
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    public function sendPushNotification(Request $request)
    {
        //     $validatedData = $request->validate([
        //         'device_id' => 'required|string',
        //         'notification_message' => 'required|array',
        //         'notification_message.title' => 'required|string',
        //         'notification_message.body' => 'required|string',
        //         'notification_data' => 'required|array',
        //     ]);

        //     $deviceId = $validatedData['device_id'];
        //     $notificationMessageArray = $validatedData['notification_message'];
        //     $notificationDataArray = $validatedData['notification_data'];

        //     try {
        //         // Call the function to send the notification
        //         $response = sendAppPushNotification(1, $deviceId, $notificationMessageArray, $notificationDataArray);

        //         Log::info("Test Push ============================");
        //         Log::info("Push notification sent successfully to device: {$deviceId}");

        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Notification sent successfully',
        //             'response' => $response,
        //         ], 200);
        //     } catch (\Exception $e) {
        //         Log::error("Error sending push notification: {$e->getMessage()}");

        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Failed to send notification',
        //             'error' => $e->getMessage(),
        //         ], 500);
        //     }
    }


}
