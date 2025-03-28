<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Notifications\DynamicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;
use Spatie\Permission\Models\Permission;

class EventController extends Controller
{

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Event Index Accessed', description: 'Accessing event index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'title');

            $events = Event::with('society', 'createdBy')
                ->searchByStatus($status)
                ->when($search && $search_col, function ($query) use ($search, $search_col) {
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->where('society_id', $selectedSociety);

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate = Carbon::parse($request->to_date)->endOfDay();

                // Filter for specific checkin and checkout range
                $events = $events->where(function ($query) use ($fromDate, $toDate) {
                    $query->where('date', '>=', $fromDate)
                        ->where('date', '<=', $toDate);
                });
            }

            $events = $events->orderBy('id', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Event Retrieved', description: 'Event retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::event.event', [
                'events' => $events,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Event Index Error', description: 'Exception during event index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Event Creation Started', description: 'Starting the process of creating a new event');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'sub_title' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'date' => 'required|date',
                'time' => 'required',
                'description' => 'required|string',
                'society_id' => 'required|integer',
                'created_by' => 'required|integer',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Event Creation Validation Failed', description: 'Validation error during event creation: ' . $validator->errors()->first(), modelType: 'Event', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create event
            ini_set('upload_max_filesize', '5M');
            ini_set('post_max_size', '5M');
            $path = $request->file('image')->store('events', 'public');
            $event = new Event();
            $event->title = $request->input('title');
            $event->sub_title = $request->input('sub_title');
            $event->date = $request->input('date');
            $event->time = $request->input('time');
            $event->image = $path;
            $event->description = $request->input('description');
            $event->society_id = $request->input('society_id');
            $event->created_by = $request->input('created_by');
            $event->status = 'active'; // Default status
            $event->save();

            $event = Event::with('society')->find($event->id);
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // send alert to all admin
            $checkSett = 'new_event_notifications';
            //get superAdmins
            $superAdmins = User::where('role', 'admin')
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
                            'title' => 'New Event Announcement',
                            'body' => $event->title,
                            'model' => 'Event',
                            'model_id' => $event->id,
                            'society_name' => $event->society->name,
                            'society_id' => $event->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================
            // send to all app users whose have enabled sett.
            User::whereHas('notificationSettings', function ($query) use ($checkSett, $event) {
                $query->where('name', $checkSett)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $event->society_id);
            })
                ->chunk(200, function ($users) use ($event) {
                    foreach ($users as $notifyUser) {
                        if ($notifyUser->device_id) {
                            $deviceId = $notifyUser->device_id;
                            $notificationMessageArray = [
                                'title' => 'New Event Announcement',
                                'body' => $event->title,
                            ];

                            // Dispatch the job to send the push notification asynchronously
                            SendPushNotificationJob::dispatch($notifyUser->id, $deviceId, $notificationMessageArray);
                        }
                    }
                });

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%
            $filteredRequestData = $request->except('image');
            _dLog(eventType: 'info', activityName: 'Event Created', description: 'New event created', modelType: 'Event', modelId: $event->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $event->toArray(), requestData: $filteredRequestData);

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Event added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Event Creation Failed', description: 'Exception during event creation: ' . $e->getMessage(), modelType: 'Event', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $event = Event::with('createdBy', 'society')->find($id);

            if (!$event) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }


            _dLog(eventType: 'info', activityName: 'Event Details Accessed', description: 'Accessing details of event ', modelType: 'Event', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::event.details',
                compact('event')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Event Details Error', description: 'Exception during event details retrieval: ' . $e->getMessage(), modelType: 'Event', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Event Edit Accessed', description: 'Accessing edit page for event ', modelType: 'Event', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $event,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Event Edit Error', description: 'Exception during event edit retrieval: ' . $e->getMessage(), modelType: 'Event', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Event Update Started', description: 'Starting the process of update event', modelType: 'Event', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'sub_title' => 'required|string',
                'image' => 'sometimes|image|mimes:png,jpg,jpeg|max:5120',
                'date' => 'required|date',
                'time' => 'required',
                'description' => 'required|string',
                'society_id' => 'required|integer',
                'created_by' => 'required|integer',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Event Update Validation Failed', description: 'Validation error during event update', modelType: 'Event', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $event = Event::find($id);
            if (!$event) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            ini_set('upload_max_filesize', '5M');
            ini_set('post_max_size', '5M');

            if ($request->hasFile('image') && $request->hasFile('image') != null) {
                $path = $request->file('image')->store('events', 'public');

                $oldImg = $event->image;

                if ($oldImg && Storage::disk('public')->exists($oldImg)) {
                    Storage::disk('public')->delete($oldImg);
                }

                $event->image = $path;
            }

            $event->title = $request->title;
            $event->sub_title = $request->sub_title;
            $event->date = $request->date;
            $event->time = $request->time;
            $event->description = $request->description;
            $event->created_by = $request->created_by;
            $event->save();

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Event Updated', description: 'Event updated ( Title : ' . $event->title . ' ) ', modelType: 'Event', modelId: $event->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $event->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Event Update Failed', description: 'Exception during event update: ' . $e->getMessage(), modelType: 'Event', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'Event Deletion Started', description: 'Starting the process of deleting ', modelType: 'Event', modelId: $id);

            DB::beginTransaction();
            $event = Event::find($id);
            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $event->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Event Deleted', description: 'Event deleted ( Title : ' . $event->title . ' ) ', modelType: 'Event', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Event Deletion Failed', description: 'Exception during event deletion: ' . $e->getMessage(), modelType: 'Event', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
