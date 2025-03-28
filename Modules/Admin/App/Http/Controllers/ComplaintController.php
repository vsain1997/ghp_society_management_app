<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Carbon\Carbon;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class ComplaintController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:complaints.view|complaints.assign')->only(['index', 'show']);
        $this->middleware('permission:complaints.assign')->only(['assignServiceProvider']);
    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Complaint Index Accessed', description: 'Accessing complaint index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            $complaintsQuery = Complaint::with([
                'society',
                'complaintBy',
                'serviceCategory',
                'staff' => function ($query) use ($selectedSociety) {
                    $query->where('society_id', $selectedSociety);
                },
                'assignedTo'
            ])
                ->where('society_id', $selectedSociety);

            $category_id = '';
            if ($request->filled('category')) {
                $category_id = $request->category;
                $complaintsQuery->where('complaint_category_id', $category_id);
            }

            $status = '';
            if ($request->filled('status')) {
                $status = $request->status;
                $complaintsQuery->where('status', $status);
            }

            $searchText = '';
            if ($request->filled('search')) {
                $searchText = $request->search;
                $complaintsQuery->where(function ($query) use ($searchText) {
                    $query->whereHas('complaintBy', function ($subQuery) use ($searchText) {
                        $subQuery->where('name', 'LIKE', '%' . $searchText . '%')
                            ->orWhere('phone', 'LIKE', '%' . $searchText . '%');
                    })
                        ->orWhereHas('assignedTo', function ($subQuery) use ($searchText) {
                            $subQuery->where('name', 'LIKE', '%' . $searchText . '%');
                        });
                });
            }

            $complaints = $complaintsQuery
                ->orderBy('id', 'desc')
                ->paginate(25);

            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $selectedSociety)
                ->get();

            $complaintCategories = ComplaintCategory::all();

            _dLog(eventType: 'info', activityName: 'Complaints Retrieved', description: 'Complaints retrieved', status: 'success', severityLevel: 1);

            return view('admin::complaint.complaint', [
                'complaints' => $complaints,
                'societyResidents' => $societyResidents,
                'complaintCategories' => $complaintCategories,
                'category' => $category_id,
                'search' => $searchText,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Complaint Index Error', description: 'Exception during complaint index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $complaint = Complaint::with('society', 'complaintBy', 'serviceCategory', 'assignedTo', 'complaintFiles')->find($id);

            if (!$complaint) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Complaint Details Accessed', description: 'Accessing details of complaint ', modelType: 'Complaint', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::complaint.details',
                compact('complaint')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Complaint Details Error', description: 'Exception during complaint details retrieval: ' . $e->getMessage(), modelType: 'Complaint', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function assignServiceProvider(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Complaint Assign Service Provider Started', description: 'Assign service provider for complaint');

            $validator = Validator::make($request->all(), [
                'staff_user_id' => 'required',
                'complaint_id' => 'required',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Complaint Assign Service Provider Validation Failed', description: 'Validation error during complaint assign service provider', modelType: 'Complaint', modelId: $request->complaint_id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $complaint = Complaint::find($request->complaint_id);
            if (!$complaint) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No Complaint Found',
                ]);
            }

            if ($complaint->status == 'in_progress') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not Allowed ! Already In Progress',
                ]);
            }

            if ($complaint->status == 'done') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not Allowed ! Already Resolved',
                ]);
            }

            if ($complaint->status == 'cancelled') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not Allowed ! Already Cancelled',
                ]);
            }

            $complaint->status = 'assigned';
            $complaint->assigned_to = $request->staff_user_id;
            $complaint->assigned_at = Carbon::now('Asia/Kolkata');
            $complaint->save();

            _dLog(eventType: 'info', activityName: 'Assign Service Provider To Complaint', description: 'Assign Service Provider To Complaint', modelType: 'Complaint', modelId: $complaint->id, status: 'success', severityLevel: 1, beforeData: '', afterData: $complaint->toArray(), requestData: $request->all());

            $complaint = Complaint::with('assignedTo')->find($complaint->id);

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            // send to service provider
            $checkSett = 'assigned_service_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $complaint) {
                $query->where('name', $checkSett)
                    ->where('user_id', $complaint->assigned_to)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $complaint->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;
                $notificationMessageArray = [
                    'title' => 'New Service Request Assigned',
                    'body' => 'Check the details in your app dashboard',
                ];

                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            //============================================================
            //inform notification to complaint by resident
            $checkSett = 'complaint_related_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $complaint) {
                $query->where('name', $checkSett)
                    ->where('user_id', $complaint->complaint_by)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $complaint->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;
                $notificationMessageArray = [
                    'title' => 'Service Provider Assigned',
                    'body' => $complaint->assignedTo->name . ' has been assigned to resolve your complaint',
                ];

                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            return response()->json([
                'status' => 'success',
                'message' => 'Service Provider assigned successfully',
            ]);
        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Complaint Assign Service Provider', description: 'Exception during Assign Service Provider: ' . $e->getMessage(), modelType: 'Complaint', modelId: $request->complaint_id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }
}
