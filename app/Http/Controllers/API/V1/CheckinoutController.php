<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\CheckinDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckinoutController extends Controller
{
    public function residentCheckIn(Request $request)
    {
        $now = Carbon::now('Asia/Kolkata');
        $currentDateTime = $now->format('Y-m-d H:i:s');

        try {
            // rule
            $validator = validator($request->all(), array_merge([
                'user_id' => 'required',
                'entry_type' => 'required',
                // 'type' => 'required|in:resident,daily_help'
            ]));

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user = User::find($request->user_id);
            if(!$user){
                return res(
                    status: false,
                    message: 'User does not exist',
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $alreadyCheckedIn = CheckinDetail::where('status', 'checked_in')
            ->whereNull('checkout_at')
            ->where('by_resident', $request->user_id)
            ->orderBy('id', 'desc')->first();

            if($alreadyCheckedIn){
                return res(
                    status: false,
                    message: 'User is already checked in at '.$alreadyCheckedIn->checkin_at,
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }


            if (auth()->user()->role === 'staff_security_guard') {
                if ($request->filled('type') && $request->type == 'daily_help') {

                    // Create checkin details
                    $checkin = CheckinDetail::create([
                        'status' => 'checked_in',
                        'checkin_at' => $currentDateTime,
                        'checkin_by' => auth()->id(),//security guard
                        'society_id' => auth()->user()->society_id,
                        'by_daily_help' => $request->user_id,
                        'checkin_type' => $request->entry_type
                    ]);

                    $checkin = CheckinDetail::with([
                        'dailyHelp' => function ($query) {
                            $query->select('users.*')->with([
                                'staff' => function ($query) {
                                    $query->select('staffs.*'); // Selects all columns from staffs
                                }
                            ]);
                        }
                    ])->find($checkin->id);

                } else {
                    // Create checkin details
                    $checkin = CheckinDetail::create([
                        'status' => 'checked_in',
                        'checkin_at' => $currentDateTime,
                        'checkin_by' => auth()->id(),//security guard
                        'society_id' => auth()->user()->society_id,
                        'by_resident' => $request->user_id,
                        'checkin_type' => $request->entry_type
                    ]);

                    $checkin = CheckinDetail::with([
                        'resident' => function ($query) {
                            $query->select(
                                'users.*'
                            )->with([
                                        'member' => function ($query) {
                                            $query->select(
                                                'members.id',
                                                'members.user_id',
                                                'members.name',
                                                'members.aprt_no',
                                                'members.floor_number',
                                                'members.unit_type',
                                                'members.phone',
                                                'blocks.name as block_name'
                                            )->join('blocks', 'members.block_id', '=', 'blocks.id');
                                        }
                                    ]);
                        }
                    ])->find($checkin->id);

                }
            } elseif (auth()->user()->role === 'resident' || auth()->user()->role === 'admin') {

                if ($request->filled('type') && $request->type == 'daily_help') {

                    // Create checkin details
                    $checkin = CheckinDetail::create([
                        'status' => 'in',
                        'checkin_at' => $currentDateTime,
                        'checkin_by' => auth()->id(),//member ownself
                        'society_id' => auth()->user()->society_id,
                        'by_daily_help' => $request->user_id,
                        'daily_help_for_member' => auth()->id(),
                        'checkin_type' => $request->entry_type
                    ]);

                    $checkin = CheckinDetail::with([
                        'dailyHelp' => function ($query) {
                            $query->select('users.*')->with([
                                'staff' => function ($query) {
                                    $query->select('staffs.*'); // Selects all columns from staffs
                                }
                            ]);
                        }
                    ])->find($checkin->id);

                }
            }

            $data = [
                'checkin' => $checkin,
            ];

            return res(
                status: true,
                message: 'Checked In successfull.',
                data: $data,
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

    public function residentCheckOut(Request $request)
    {
        $now = Carbon::now('Asia/Kolkata');
        $currentDateTime = $now->format('Y-m-d H:i:s');

        try {
            // rule
            $validator = validator($request->all(), array_merge([
                'user_id' => 'required',
                'entry_type' => 'required',
            ]));

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user = User::find($request->user_id);
            if(!$user){
                return res(
                    status: false,
                    message: 'User does not exist',
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if (auth()->user()->role === 'staff_security_guard') {

                if ($request->filled('type') && $request->type == 'daily_help') {

                    // checkout
                    $doCheckout = CheckinDetail::where('status', 'checked_in')->where('by_resident', $request->user_id)->orderBy('id', 'desc')->first();
                    if (!$doCheckout) { // If no active check-in is found
                        return res(
                            status: false,
                            message: 'User is not checked in', // More accurate message
                            code: HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    $alreadyCheckout = CheckinDetail::where('status', 'checked_out')->where('by_resident', $request->user_id)->orderBy('id', 'desc')->first();
                    if($alreadyCheckout){
                        return res(
                            status: false,
                            message: 'User is already checked out',
                            code: HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    $doCheckout->status = 'checked_out';
                    $doCheckout->checkout_at = $currentDateTime;
                    $doCheckout->checkout_by = auth()->id();
                    $doCheckout->society_id = auth()->user()->society_id;
                    $doCheckout->checkout_type = $request->entry_type;
                    $doCheckout->save();

                    $checkout = CheckinDetail::with([
                        'dailyHelp' => function ($query) {
                            $query->select('users.*')->with([
                                'staff' => function ($query) {
                                    $query->select('staffs.*'); // Selects all columns from staffs
                                }
                            ]);
                        }
                    ])->find($doCheckout->id);

                } else {

                    // checkout
                    $doCheckout = CheckinDetail::orderBy('id', 'desc')->where('by_resident', $request->user_id)->first();

                    if (!$doCheckout) { // If no active check-in is found
                        return res(
                            status: false,
                            message: 'User is not checked in', // More accurate message
                            code: HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    $alreadyCheckout = CheckinDetail::where('status', 'checked_in')
                    ->whereNull('checkout_at')
                    ->where('by_resident', $request->user_id)
                    ->orderBy('id', 'desc')
                    ->first();

                    if(!$alreadyCheckout){
                        return res(
                            status: false,
                            message: 'User is already checked out at '. $doCheckout->checkout_at,
                            code: HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    $doCheckout->status = 'checked_out';
                    $doCheckout->checkout_at = $currentDateTime;
                    $doCheckout->checkout_by = auth()->id();
                    $doCheckout->society_id = auth()->user()->society_id;
                    $doCheckout->checkout_type = $request->entry_type;
                    $doCheckout->save();

                    $checkout = CheckinDetail::with([
                        'resident' => function ($query) {
                            $query->select(
                                'users.*'
                            )->with([
                                        'member' => function ($query) {
                                            $query->select(
                                                'members.id',
                                                'members.user_id',
                                                'members.name',
                                                'members.aprt_no',
                                                'members.floor_number',
                                                'members.unit_type',
                                                'members.phone',
                                                'blocks.name as block_name'
                                            )->join('blocks', 'members.block_id', '=', 'blocks.id');
                                        }
                                    ]);
                        }
                    ])->find($doCheckout->id);

                }
            } elseif (auth()->user()->role === 'resident' || auth()->user()->role === 'admin') {

                if ($request->filled('type') && $request->type == 'daily_help') {

                    $doCheckout = CheckinDetail::where('status', 'in')->orderBy('id', 'desc')->where('by_resident', $request->user_id)->first();
                    if (!$doCheckout) { // If no active check-in is found
                        return res(
                            status: false,
                            message: 'User is not checked in', // More accurate message
                            code: HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    $alreadyCheckout = CheckinDetail::where('status', 'out')->where('by_resident', $request->user_id)->orderBy('id', 'desc')->first();
                    if($alreadyCheckout){
                        return res(
                            status: false,
                            message: 'User is already checked out',
                            code: HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    $doCheckout->status = 'out';
                    $doCheckout->checkout_at = $currentDateTime;
                    $doCheckout->checkout_by = auth()->id();
                    $doCheckout->society_id = auth()->user()->society_id;
                    $doCheckout->checkout_type = $request->entry_type;
                    $doCheckout->save();

                    $checkout = CheckinDetail::with([
                        'dailyHelp' => function ($query) {
                            $query->select('users.*')->with([
                                'staff' => function ($query) {
                                    $query->select('staffs.*'); // Selects all columns from staffs
                                }
                            ]);
                        }
                    ])->find($doCheckout->id);

                }
            }

            $data = [
                'checkout' => $checkout,
            ];

            return res(
                status: true,
                message: 'Checked Out successfull.',
                data: $data,
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

    public function residentCheckinoutLog(Request $request)
    {
        try {

            $now = Carbon::now('Asia/Kolkata');
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $user_role = auth()->user()->role;

            // Base query
            $query = CheckinDetail::with([
                'resident' => function ($query) {
                    $query->select(
                        'users.*'
                    )->with([
                                'member' => function ($query) {
                                    $query->select(
                                        'members.id',
                                        'members.user_id',
                                        'members.name',
                                        'members.aprt_no',
                                        'members.floor_number',
                                        'members.unit_type',
                                        'members.phone',
                                        'blocks.name as block_name'
                                    )->join('blocks', 'members.block_id', '=', 'blocks.id');
                                }
                            ]);
                }
            ])->where('society_id', $society_id)
                // ->where('status', 'checked_in')//means resident checkout
                ->whereNull('visitor_id')//means resident checkout
                ->whereNull('parcel_id')//means resident checkout
                ->whereNotNull('by_resident');//means resident checkout

            // Role-specific filtering
            if ($user_role === 'resident' || $user_role === 'admin') {
                $query->where('by_resident', $user_id);
                //resident_delete_status is null
                $query->whereNull('resident_delete_status');//hide deleted parcels from resident only
            } elseif ($user_role === 'staff_security_guard') {
                //
            }

            $query = $query->whereIn('id', function ($subquery) {
                $subquery->selectRaw('MAX(id)')
                    ->from('checkin_details')
                    ->groupBy('by_resident');
            })->orderBy('checkin_at', 'desc');

            $checkin_logs = $query->paginate(25);

            // Check if parcels are found
            if ($checkin_logs->isEmpty()) {
                return res(
                    status: false,
                    message: "No logs found!",
                    data: null,
                    code: HTTP_OK
                );
            }

            $data = [
                'checkin_logs' => $checkin_logs
            ];
            // Return response
            return res(
                status: true,
                message: "Log retrieved successfully.",
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

    public function residentCheckinoutDetails(Request $request, $user_id = null)
    {
        try {
            if ($user_id == null) {
                $user_id = auth()->id();
            }

            $validator = validator($request->all(), [
                'from_date' => 'required_with:to_date|date',
                'to_date' => 'required_with:from_date|date|after_or_equal:from_date',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Set default values if both are missing
            $now = Carbon::now('Asia/Kolkata');
            $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : $now->copy()->startOfDay();
            $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : $now->copy()->endOfDay();

            // Fetch user details only once
            $user = User::where('id', $user_id)
                ->with([
                    'member' => function ($query) {
                        $query->select(
                            'members.id',
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
                ->first();

            // Fetch check-in logs for this user
            $checkin_logs = CheckinDetail::where('by_resident', $user_id);
            if ($request->filled('from_date') && $request->filled('to_date')) {

                $checkin_logs = $checkin_logs->where('checkin_at', '>=', $fromDate)  // Start date filter
                    ->where('checkin_at', '<=', $toDate);    // End date filter
            }
            $checkin_logs = $checkin_logs->orderBy('checkin_at', 'desc')
                ->get();

            // Check if logs are found
            // if ($checkin_logs->isEmpty()) {
            //     return res(
            //         status: false,
            //         message: "No log found!",
            //         code: HTTP_NOT_FOUND
            //     );
            // }

            // Return the response in JSON format
            $data = [
                'user' => $user,       // Single user data
                'logs' => $checkin_logs // List of logs
            ];

            return res(
                status: true,
                message: "Log details retrieved successfully",
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
    public function dailyHelpCheckinoutLog(Request $request)
    {
        try {

            $now = Carbon::now('Asia/Kolkata');
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;
            $user_role = auth()->user()->role;

            // Base query
            $query = CheckinDetail::with([
                'resident' => function ($query) {
                    $query->select(
                        'users.*'
                    )->with([
                                'staff' => function ($query) {
                                    $query->select('staffs.*'); // Selects all columns from staffs
                                }
                            ]);
                }
            ])->where('society_id', $society_id)
                ->whereNull('visitor_id')//means visitor checkout
                ->whereNull('parcel_id')//means parcel checkout
                ->whereNull('by_resident')//means resident checkout
                ->whereNotNull('by_daily_help');//means resident checkout

            // Role-specific filtering
            if ($user_role === 'resident' || $user_role === 'admin') {
                $query->where('daily_help_for_member', $user_id);
                //resident_delete_status is null
                $query->whereNull('resident_delete_status');//hide deleted parcels from resident only
            } elseif ($user_role === 'staff_security_guard') {
                //
            }

            $query = $query->whereIn('id', function ($subquery) {
                $subquery->selectRaw('MAX(id)')
                    ->from('checkin_details')
                    ->groupBy('by_daily_help');
            })->orderBy('checkin_at', 'desc');

            $checkin_logs = $query->paginate(25);

            // Check if parcels are found
            if ($checkin_logs->isEmpty()) {
                return res(
                    status: false,
                    message: "No logs found!",
                    data: null,
                    code: HTTP_OK
                );
            }

            $data = [
                'checkin_logs' => $checkin_logs
            ];
            // Return response
            return res(
                status: true,
                message: "Log retrieved successfully.",
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

    public function dailyHelpCheckinoutDetails(Request $request, $user_id = null)
    {
        try {
            if ($user_id == null) {
                $user_id = auth()->id();
            }

            $validator = validator($request->all(), [
                'from_date' => 'required_with:to_date|date',
                'to_date' => 'required_with:from_date|date|after_or_equal:from_date',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Set default values if both are missing
            $now = Carbon::now('Asia/Kolkata');
            $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : $now->copy()->startOfDay();
            $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : $now->copy()->endOfDay();

            // Fetch user details only once
            $user = User::where('id', $user_id)
                ->with([
                    'staff' => function ($query) {
                        $query->select('staffs.*'); // Selects all columns from staffs
                    }
                ])
                ->first();

            // Fetch check-in logs for this user
            $checkin_logs = CheckinDetail::with([
                'dailyHelpMemberDetails' => function ($query) {
                    $query->select('users.*')->with([
                        'member' => function ($query) {
                            $query->select(
                                'members.id',
                                'members.user_id',
                                'members.name',
                                'members.aprt_no',
                                'members.floor_number',
                                'members.unit_type',
                                'members.phone',
                                'blocks.name as block_name'
                            )->join('blocks', 'members.block_id', '=', 'blocks.id');
                        }
                    ]);
                },
                'checkedInBy',
                'checkedOutBy'
            ])->where('by_daily_help', $user_id);

            if ($request->filled('from_date') && $request->filled('to_date')) {

                $checkin_logs = $checkin_logs->where('checkin_at', '>=', $fromDate)  // Start date filter
                    ->where('checkin_at', '<=', $toDate);    // End date filter
            }

            $checkin_logs = $checkin_logs->orderBy('checkin_at', 'desc')
                ->get();

            $checkin_logs->each(function ($log) {
                if ($log->dailyHelpMemberDetails) {
                    // $log->dailyHelpMemberDetails->setAppends([]); // Remove all appended attributes
                    $log->dailyHelpMemberDetails->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']); // Ensure it's hidden
                }
                if ($log->checkedInBy) {
                    $log->checkedInBy->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
                }

                if ($log->checkedOutBy) {
                    $log->checkedOutBy->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
                }
            });
            // Organize logs by check-in date
            $formattedLogs = [];

            foreach ($checkin_logs as $log) {
                $date = date('Y-m-d', strtotime($log->checkin_at)); // Grouping key

                // Initialize date entry if not exists
                if (!isset($formattedLogs[$date])) {
                    $formattedLogs[$date] = [
                        "security_staff_logs" => [],
                        "member_logs" => []
                    ];
                }

                if (auth()->user()->role == 'admin' || auth()->user()->role == 'resident') {
                    // Categorize logs
                    if (in_array($log->status, ['checked_in', 'checked_out'])) {
                        $formattedLogs[$date]["security_staff_logs"][] = $log;
                    } elseif (in_array($log->status, ['in', 'out']) && in_array($log->checkin_by, [auth()->id()])) {
                        $formattedLogs[$date]["member_logs"][] = $log;
                    }

                } else {
                    // Categorize logs
                    if (in_array($log->status, ['checked_in', 'checked_out'])) {
                        $formattedLogs[$date]["security_staff_logs"][] = $log;
                    } elseif (in_array($log->status, ['in', 'out'])) {
                        $formattedLogs[$date]["member_logs"][] = $log;
                    }
                }

            }

            // Convert to indexed array format
            $finalResult = array_values($formattedLogs);

            // Check if logs are found
            // if ($checkin_logs->isEmpty()) {
            //     return res(
            //         status: false,
            //         message: "No log found!",
            //         code: HTTP_NOT_FOUND
            //     );
            // }

            // Return the response in JSON format
            $data = [
                'user' => $user,       // Single user data
                'logs' => $finalResult // List of logs
            ];

            return res(
                status: true,
                message: "Log details retrieved successfully",
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

    // public function residentCheckinoutDetails($user_id)
    // {
    //     try {
    //         // Start building the query
    //         $query = CheckinDetail::with([
    //             'resident' => function ($query) {
    //                 $query->select(
    //                     'users.*'
    //                 )->with([
    //                             'member' => function ($query) {
    //                                 $query->select(
    //                                     'members.id',
    //                                     'members.user_id',
    //                                     'members.name',
    //                                     'members.aprt_no',
    //                                     'members.floor_number',
    //                                     'members.unit_type',
    //                                     'members.phone',
    //                                     'blocks.name as block_name'
    //                                 )->join('blocks', 'members.block_id', '=', 'blocks.id');
    //                             }
    //                         ]);
    //             }
    //         ])->where('by_resident', $user_id);

    //         $log = $query->orderBy('checkin_at', 'desc')
    //             ->get();

    //         // Check if parcel are found
    //         if ($log->isEmpty()) {
    //             return res(
    //                 status: false,
    //                 message: "No log found!",
    //                 code: HTTP_NOT_FOUND
    //             );
    //         }

    //         // Return the response in JSON format
    //         $data = [
    //             'log' => $log,
    //         ];
    //         return res(
    //             status: true,
    //             message: "Log details retrieved successfully",
    //             data: $data,
    //             code: HTTP_OK
    //         );

    //     } catch (\Exception $e) {
    //         // Handle any exceptions that occur
    //         return res(
    //             status: false,
    //             message: $e->getMessage(),
    //             code: HTTP_INTERNAL_SERVER_ERROR
    //         );
    //     }
    // }
}
