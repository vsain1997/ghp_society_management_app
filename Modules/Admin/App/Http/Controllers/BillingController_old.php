<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Models\Bill;
use App\Models\BillService;
use App\Models\Member;
use App\Notifications\DynamicNotification;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class BillingController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:billing.view|billing.create|billing.edit|billing.delete')->only(['index', 'show']);
        $this->middleware('permission:billing.create')->only(['store']);
        $this->middleware('permission:billing.edit')->only(['edit', 'update']);
        $this->middleware('permission:billing.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Billing Index Accessed', description: 'Accessing billing index page');

            $selectedSociety = getSelectedSociety($request);

            // if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
            //     return $selectedSociety; // Redirect if necessary
            // }
            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }
            //get all society residents
            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.role', 'resident')
                ->where('members.society_id', $selectedSociety)
                ->get();

            // get $billServices
            $billServices = BillService::all();

            $status = $request->input(key: 'status', default: 'unpaid');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            $bills = Bill::with('user', 'service')
                ->searchByStatus($status)
                ->when($search, function ($query) use ($search) {
                    return $query->whereHas('service', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    });
                })
                ->where('society_id', $selectedSociety);
            if ($request->filled('user_id')) {
                $bills = $bills->searchByResident($request->user_id);
            }
            $bills = $bills->orderBy('id', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Bills List Retrieved', description: 'Bills list retrieved', status: 'success', severityLevel: 1);

            return view('admin::bill.bill', [
                'bills' => $bills,
                'search' => $search,
                'societyResidents' => $societyResidents,
                'billServices' => $billServices,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Notice Index Error', description: 'Exception during notice index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Bill Creation Started', description: 'Starting the process of creating a new bill');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'service_id' => 'required|exists:bill_services,id',
                'bill_type' => 'required|in:my_bill,maintenance',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date|after_or_equal:' . now()->setTimezone('Asia/Kolkata')->toDateString(),
                'society_id' => 'required|integer',
                'created_by' => 'required|integer',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Bill Creation Validation Failed', description: 'Validation error during bill creation: ' . $validator->errors()->first(), modelType: 'Bill', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create notice
            $bill = Bill::create([
                ...$request->all(),
                'status' => 'unpaid',
                'created_by' => auth()->id(),
                // 'society_id' => auth()->user()->society_id,
            ]);

            $bill = Bill::with('user', 'society')->find($bill->id);
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            //inform notification to resident
            $checkSett = 'bill_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $bill) {
                $query->where('name', $checkSett)
                    ->where('user_id', $bill->user_id)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $bill->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;
                $notificationMessageArray = [
                    'title' => 'New Bill Added',
                    'body' => "A bill of ₹ " . $bill->amount . " has been added",
                ];

                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            _dLog(eventType: 'info', activityName: 'Bill Created', description: 'New bill created', modelType: 'Bill', modelId: $bill->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $bill->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Bill added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Bill Creation Failed', description: 'Exception during bill creation: ' . $e->getMessage(), modelType: 'Bill', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $bill = Bill::with('service', 'user', 'society')->find($id);

            if (!$bill) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            $residentOtherInfo = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.user_id', $bill->user_id)
                ->first();

            _dLog(eventType: 'info', activityName: 'Bill Details Accessed', description: 'Accessing details of bill ', modelType: 'Bill', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::bill.details',
                compact('bill', 'residentOtherInfo')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Bill Details Error', description: 'Exception during bill details retrieval: ' . $e->getMessage(), modelType: 'Bill', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $bill = Bill::find($id);

            if (!$bill) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Bill Edit Accessed', description: 'Accessing edit page for bill ', modelType: 'Bill', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $bill,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Bill Edit Error', description: 'Exception during bill edit retrieval: ' . $e->getMessage(), modelType: 'Bill', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Bill Update Started', description: 'Starting the process of update bill', modelType: 'Bill', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'service_id' => 'required|exists:bill_services,id',
                'bill_type' => 'required|in:my_bill,maintenance',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date|after_or_equal:' . now()->setTimezone('Asia/Kolkata')->toDateString(),
                'society_id' => 'required|integer',
                'created_by' => 'required|integer',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Bill Update Validation Failed', description: 'Validation error during bill update', modelType: 'Bill', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $bill = Bill::find($id);
            if (!$bill) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            $bill->update($request->only([
                'user_id',
                'service_id',
                'bill_type',
                'amount',
                'due_date',
                'society_id',
                'created_by',
            ]));

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Bill Updated', description: 'Bill updated ( Title : ' . $bill->title . ' ) ', modelType: 'Bill', modelId: $bill->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $bill->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Bill updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Bill Update Failed', description: 'Exception during bill update: ' . $e->getMessage(), modelType: 'Bill', modelId: $id, status: 'failed', severityLevel: 2);

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Bill Deletion Started', description: 'Starting the process of deleting ', modelType: 'Bill', modelId: $id);

            DB::beginTransaction();
            $bill = Bill::with('user')->find($id);
            if (!$bill) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }
            $bill->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Bill Deleted', description: 'Bill deleted ( Member : ' . $bill->user->name . ' ) ', modelType: 'Bill', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Bill Deletion Failed', description: 'Exception during bill deletion: ' . $e->getMessage(), modelType: 'Bill', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Bill Status Change Started', description: 'Starting the process of status change ', modelType: 'Bill', modelId: $id);

            $bill = Bill::find($id);
            if (!$bill) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $bill->status = 'paid';
            $bill->save();

            _dLog(eventType: 'info', activityName: 'Bill Paid', description: 'Bill Paid ( Member : ' . $bill->user->name . ' ) ', modelType: 'Bill', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Bill Paid Failed', description: 'Exception during bill paid status chnage: ' . $e->getMessage(), modelType: 'Bill', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again!'
            ]);
        }
    }
}
