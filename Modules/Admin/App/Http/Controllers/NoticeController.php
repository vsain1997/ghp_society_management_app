<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Notifications\DynamicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class NoticeController extends Controller
{

    public function __construct()
    {
        /**
         * if any permission active , then view permission will come as default.
         */
        // $user = auth()->id;


        // Get all permissions assigned to the user
        // $permissions = $user->getAllPermissions();
        // dd($permissions);
        // $this->middleware('permission:notice.view')->only(['index', 'show']);
        // $this->middleware('permission:notice.create')->only(['store', 'index', 'show']);
        // $this->middleware('permission:notice.edit')->only(['edit', 'update', 'index', 'show']);
        // $this->middleware('permission:notice.delete')->only(['destroy', 'index', 'show']);
        // $this->middleware('permission:notice.status_change')->only(['changeStatus', 'index', 'show']);
        $this->middleware('permission:notice.view|notice.create|notice.edit|notice.delete|notice.status_change')->only(['index', 'show']);
        $this->middleware('permission:notice.create')->only(['store']);
        $this->middleware('permission:notice.edit')->only(['edit', 'update']);
        $this->middleware('permission:notice.delete')->only(['destroy']);
        $this->middleware('permission:notice.status_change')->only(['changeStatus']);

        // $this->middleware(function ($request, $next) {
        //     // Get permissions directly assigned to the user in model_has_permissions
        //     $permissions = DB::table('model_has_permissions')
        //         ->where('model_id', auth()->id())
        //         ->where('model_type', get_class(auth()->user()))
        //         ->pluck('permission_id'); // Get permission IDs

        //     // Fetch the permission names using the IDs
        //     $permissionNames = Permission::whereIn('id', $permissions)->pluck('name')->toArray();

        //     // Debug the permissions
        //     dd($permissionNames);

        //     return $next($request);
        // });
        // $this->middleware('permission:notice.view|notice.create')->only(['index', 'show']);
        // $this->middleware('permission:notice.create')->only(['store']);
        // $this->middleware('permission:notice.edit|notice.create')->only(['edit', 'update']);
        // $this->middleware('permission:notice.delete')->only(['destroy']);
        // $this->middleware('permission:notice.status_change')->only(['changeStatus']);

        // $permissions = [
        //     'notice.view' => ['index', 'show'],
        //     'notice.create' => ['store', 'index', 'show'],
        //     'notice.edit' => ['edit', 'update', 'index', 'show'],
        //     'notice.delete' => ['destroy', 'index', 'show'],
        //     'notice.status_change' => ['changeStatus', 'index', 'show'],
        // ];

        // foreach ($permissions as $permission => $methods) {
        //     $this->middleware("permission:$permission")->only($methods);
        // }

    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Notice Index Accessed', description: 'Accessing notice index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'title');

            $notices = Notice::with('society', 'createdBy')
                ->searchByStatus($status)
                ->when($search && $search_col, function ($query) use ($search, $search_col) {
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->where('society_id', $selectedSociety);

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate = Carbon::parse($request->to_date)->endOfDay();

                // Filter for specific checkin and checkout range
                $notices = $notices->where(function ($query) use ($fromDate, $toDate) {
                    $query->where('date', '>=', $fromDate)
                        ->where('date', '<=', $toDate);
                });
            }

            $notices = $notices->orderBy('id', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Notices Retrieved', description: 'Notices retrieved', status: 'success', severityLevel: 1);

            return view('admin::notice.notice', [
                'notices' => $notices,
                'search' => $search,
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
            _dLog(eventType: 'info', activityName: 'Notice Creation Started', description: 'Starting the process of creating a new notice');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'date' => 'required|date',
                'time' => 'required',
                'description' => 'required|string',
                'society_id' => 'required|integer',
                'created_by' => 'required|integer',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Notice Creation Validation Failed', description: 'Validation error during notice creation: ' . $validator->errors()->first(), modelType: 'Notice', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create notice
            $notice = new Notice();
            $notice->title = $request->input('title');
            $notice->date = $request->input('date');
            $notice->time = $request->input('time');
            $notice->description = $request->input('description');
            $notice->society_id = $request->input('society_id');
            $notice->created_by = $request->input('created_by');
            $notice->status = 'active'; // Default status
            $notice->save();

            $notice = Notice::with('society')->find($notice->id);
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // send alert to all super_admin
            $checkSett = 'new_notice_notifications';
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();

            foreach ($superAdmins as $key => $notifyUser) {

                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSett, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'New Notice Published',
                            'body' => $notice->title,
                            'model' => 'Notice',
                            'model_id' => $notice->id,
                            'society_name' => $notice->society->name,
                            'society_id' => $notice->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================
            // send to all app users whose have enabled sett.
            User::whereHas('notificationSettings', function ($query) use ($checkSett, $notice) {
                $query->where('name', $checkSett)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $notice->society_id);
            })
                ->chunk(200, function ($users) use ($notice) {
                    foreach ($users as $notifyUser) {
                        if ($notifyUser->device_id) {
                            $deviceId = $notifyUser->device_id;
                            $notificationMessageArray = [
                                'title' => 'New Notice Published',
                                'body' => $notice->title,
                            ];

                            // Dispatch the job to send the push notification asynchronously
                            SendPushNotificationJob::dispatch($notifyUser->id, $deviceId, $notificationMessageArray);
                        }
                    }
                });

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            _dLog(eventType: 'info', activityName: 'Notice Created', description: 'New notice created', modelType: 'Notice', modelId: $notice->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $notice->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Notice added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Notice Creation Failed', description: 'Exception during notice creation: ' . $e->getMessage(), modelType: 'Notice', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $notice = Notice::with('createdBy', 'society')->find($id);

            if (!$notice) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }


            _dLog(eventType: 'info', activityName: 'Notice Details Accessed', description: 'Accessing details of notice ', modelType: 'Notice', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::notice.details',
                compact('notice')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Notice Details Error', description: 'Exception during notice details retrieval: ' . $e->getMessage(), modelType: 'Notice', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $notice = Notice::find($id);

            if (!$notice) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Notice Edit Accessed', description: 'Accessing edit page for notice ', modelType: 'Notice', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $notice,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Notice Edit Error', description: 'Exception during notice edit retrieval: ' . $e->getMessage(), modelType: 'Notice', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Notice Update Started', description: 'Starting the process of update notice', modelType: 'Notice', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'date' => 'required|date',
                'time' => 'required',
                'description' => 'required|string',
                'society_id' => 'required|integer',
                'created_by' => 'required|integer',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Notice Update Validation Failed', description: 'Validation error during notice update', modelType: 'Notice', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $notice = Notice::find($id);
            if (!$notice) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            $notice->update($request->only([
                'title',
                'date',
                'time',
                'description',
                'society_id',
                'created_by',
            ]));

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Notice Updated', description: 'Notice updated ( Title : ' . $notice->title . ' ) ', modelType: 'Notice', modelId: $notice->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $notice->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Notice updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Notice Update Failed', description: 'Exception during notice update: ' . $e->getMessage(), modelType: 'Notice', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'Notice Deletion Started', description: 'Starting the process of deleting ', modelType: 'Notice', modelId: $id);

            DB::beginTransaction();
            $notice = Notice::find($id);
            if (!$notice) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }
            $notice->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Notice Deleted', description: 'Notice deleted ( Title : ' . $notice->title . ' ) ', modelType: 'Notice', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Notice Deletion Failed', description: 'Exception during notice deletion: ' . $e->getMessage(), modelType: 'Notice', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Notice Status Change Started', description: 'Changing status for notice to ' . $status);

            $notice = Notice::find($id);
            if (!$notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }

            $notice->status = $status;
            $notice->save();

            _dLog(eventType: 'info', activityName: 'Notice Status Changed', description: 'Notice status updated ( Title : ' . $notice->title . ' ) ', modelType: 'Notice', modelId: $notice->id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Notice Status Change Failed', description: 'Exception during notice status change: ' . $e->getMessage(), modelType: 'Notice', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }
}
