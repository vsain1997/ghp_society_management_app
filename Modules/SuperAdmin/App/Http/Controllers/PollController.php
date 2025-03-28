<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Models\PollOption;
use App\Notifications\DynamicNotification;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;
use Spatie\Permission\Models\Permission;

class PollController extends Controller
{
    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Poll Index Accessed', description: 'Accessing event index page');

            $society_id = getSelectedSociety($request);

            if ($society_id instanceof \Illuminate\Http\RedirectResponse) {
                return $society_id; // Redirect if necessary
            }

            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'title');

            $visibility = 'published';
            $polls = Poll::with([
                'options' => function ($query) {
                    // Include vote counts for each option
                    // $query->withCount('votes');
                    $query->withCount('votes')->orderBy('votes_count', 'desc');
                }
            ])
                ->withCount('votes') // Count total votes for the poll
                ->searchByVisibility($visibility)
                ->searchBySociety($society_id);

            if (!empty($search)) {
                $polls = $polls->where('title', 'LIKE', "%{$search}%");
            }

            $polls = $polls->orderBy('end_date', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Poll Retrieved', description: 'Poll retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::poll.poll', [
                'polls' => $polls,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Poll Index Error', description: 'Exception during event index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Poll Creation Started', description: 'Starting the process of creating a new event');

            DB::beginTransaction();

            $validator = validator($request->all(), [
                'title' => 'required|string|max:255',
                // 'visibility' => 'required|in:draft,published',
                'end_date' => 'required|date',
                'options.*' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Poll Creation Validation Failed', description: 'Validation error during event creation: ' . $validator->errors()->first(), modelType: 'Poll', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create poll
            $created_by = auth()->id();
            $poll = Poll::create([
                'title' => $request->title,
                'visibility' => 'published',
                'end_date' => $request->end_date,
                'created_by' => $created_by,
                'society_id' => $request->society_id,
            ]);

            foreach ($request->options as $option) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $option,
                ]);
            }


            $poll = Poll::with('society')->find($poll->id);
            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            // send alert to admin panel
            $checkSett = 'poll_notifications';
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
                            'title' => 'New Poll Created',
                            'body' => $poll->title,
                            'model' => 'Poll',
                            'model_id' => $poll->id,
                            'society_name' => $poll->society->name,
                            'society_id' => $poll->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================
            // send to all app users whose have enabled sett.
            User::whereHas('notificationSettings', function ($query) use ($checkSett, $poll) {
                $query->where('name', $checkSett)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $poll->society_id);
            })
                ->chunk(200, function ($users) use ($poll) {
                    foreach ($users as $notifyUser) {
                        if ($notifyUser->device_id) {

                            \Log::info('Poll----Test-----User Details:', [
                                'user_id' => $notifyUser->id,
                                'user_name' => $notifyUser->name,
                                'device_id' => $notifyUser->device_id,
                            ]);

                            $deviceId = $notifyUser->device_id;
                            $notificationMessageArray = [
                                'title' => 'Cast Your Vote!',
                                'body' => $poll->title,
                            ];

                            // Dispatch the job to send the push notification asynchronously
                            SendPushNotificationJob::dispatch($notifyUser->id, $deviceId, $notificationMessageArray);
                        }
                    }
                });

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            _dLog(eventType: 'info', activityName: 'Poll Created', description: 'New event created', modelType: 'Poll', modelId: $poll->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $poll->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Poll created successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Poll Creation Failed', description: 'Exception during event creation: ' . $e->getMessage(), modelType: 'Poll', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            // $poll = Poll::with('options', 'createdBy', 'society')->find($id);
            $poll = Poll::with([
                'options' => function ($query) {
                    $query->withCount('votes')->orderBy('votes_count', 'desc');
                }
            ])
                ->find($id);


            if (!$poll) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }


            _dLog(eventType: 'info', activityName: 'Poll Details Accessed', description: 'Accessing details of event ', modelType: 'Poll', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::poll.details',
                compact('poll')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Poll Details Error', description: 'Exception during event details retrieval: ' . $e->getMessage(), modelType: 'Poll', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $poll = Poll::with('options')->find($id);

            if (!$poll) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Poll Edit Accessed', description: 'Accessing edit page for event ', modelType: 'Poll', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $poll,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Poll Edit Error', description: 'Exception during event edit retrieval: ' . $e->getMessage(), modelType: 'Poll', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Poll Update Started', description: 'Starting the process of update event', modelType: 'Poll', modelId: $id);

            DB::beginTransaction();


            $validator = validator($request->all(), [
                'title' => 'required|string|max:255',
                // 'visibility' => 'required|in:draft,published',
                'end_date' => 'required|date',
                'options.*' => 'required|string|max:255',
                'optionId.*' => 'required|integer',
            ]);


            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Poll Update Validation Failed', description: 'Validation error during event update', modelType: 'Poll', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $poll = Poll::find($id);
            if (!$poll) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            $poll->title = $request->title;
            $poll->end_date = $request->end_date;
            $poll->save();

            $existingOptionIds = $request->optionId ?: []; // Option IDs sent for update
            $newOptions = $request->options ?: []; // Option texts sent for creation


            // Update existing options
            foreach ($existingOptionIds as $key => $optionId) {
                if (!empty($optionId)) {
                    $option = PollOption::find($optionId);
                    if ($option) {
                        $option->update([
                            'option_text' => $newOptions[$key],
                        ]);
                    }
                }
            }

            // Add new options
            // foreach ($newOptions as $key => $optionText) {
            //     if (empty($existingOptionIds[$key])) {
            //         PollOption::create([
            //             'poll_id' => $poll->id,
            //             'option_text' => $optionText,
            //         ]);
            //     }
            // }

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Poll Updated', description: 'Poll updated ( Title : ' . $poll->title . ' ) ', modelType: 'Poll', modelId: $poll->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $poll->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Poll updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Poll Update Failed', description: 'Exception during event update: ' . $e->getMessage(), modelType: 'Poll', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'Poll Deletion Started', description: 'Starting the process of deleting ', modelType: 'Poll', modelId: $id);

            DB::beginTransaction();
            $data = Poll::find($id);
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $data->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Poll Deleted', description: 'Poll deleted ( Title : ' . $data->title . ' ) ', modelType: 'Poll', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Poll Deletion Failed', description: 'Exception during event deletion: ' . $e->getMessage(), modelType: 'Poll', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
