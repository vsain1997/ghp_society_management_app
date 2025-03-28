<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SosCategoryEmergencyDetail;
use Illuminate\Http\Request;
use App\Models\SosCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;
use Spatie\Permission\Models\Permission;

class SosCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:sos_category.view|sos_category.create|sos_category.edit|sos_category.delete')->only(['index', 'show']);
        $this->middleware('permission:sos_category.create')->only(['store']);
        $this->middleware('permission:sos_category.edit')->only(['edit', 'update']);
        $this->middleware('permission:sos_category.delete')->only(['destroy']);

    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'SOS Category Index Accessed', description: 'Accessing sos category index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            // $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'name');

            $sosCategorys = SosCategory::when($search && $search_col, function ($query) use ($search, $search_col) {
                return $query->where($search_col, 'LIKE', '%' . $search . '%');
            })
                ->orderBy('id', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'SOS Category Retrieved', description: 'SOS Category retrieved', status: 'success', severityLevel: 1);

            return view('admin::sosCategory.sosCategory', [
                'sosCategory' => $sosCategorys,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'SOS Category Index Error', description: 'Exception during sos category index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'SOS Category Creation Started', description: 'Starting the process of creating a new sos category');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'SOS Category Creation Validation Failed', description: 'Validation error during sos category creation: ' . $validator->errors()->first(), modelType: 'SosCategory', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create sos category
            ini_set('upload_max_filesize', '5M');
            ini_set('post_max_size', '5M');
            $path = $request->file('image')->store('sos_categories', 'public');
            $sosCategory = new SosCategory();
            $sosCategory->name = $request->input('title');
            $sosCategory->image = $path;
            $sosCategory->save();

            // ------------------
            $selectedSociety = getSelectedSociety($request);
            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }
            // actions add
            $add_type = 'action';
            $actionNames = $request->input('action_name'); //array
            foreach ($actionNames as $key => $actionName) {
                // Check if the action name is not empty
                if (!empty($actionName)) {
                    $emergencyDetail = new SosCategoryEmergencyDetail();
                    $emergencyDetail->name = $actionName;
                    $emergencyDetail->type = $add_type;
                    $emergencyDetail->sos_category_id = $sosCategory->id;
                    $emergencyDetail->society_id = $selectedSociety;
                    $emergencyDetail->save();
                }
            }
            // ------------------
            // emergency contact add
            $add_type = 'contact';
            $emerNames = $request->input('em_name'); //array
            $emerPhone = $request->input('em_phone'); //array
            foreach ($emerNames as $key => $actionName) {
                // Check if the action name is not empty
                if (!empty($actionName)) {
                    $emergencyDetail = new SosCategoryEmergencyDetail();
                    $emergencyDetail->name = $actionName;
                    $emergencyDetail->phone = $emerPhone[$key];
                    $emergencyDetail->type = $add_type;
                    $emergencyDetail->sos_category_id = $sosCategory->id;
                    $emergencyDetail->society_id = $selectedSociety;
                    $emergencyDetail->save();
                }
            }

            _dLog(eventType: 'info', activityName: 'SOS Category Created', description: 'New sos category created', modelType: 'SosCategory', modelId: $sosCategory->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $sosCategory->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'SOS Category added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'SOS Category Creation Failed', description: 'Exception during sos category creation: ' . $e->getMessage(), modelType: 'SosCategory', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $sosCategory = SosCategory::with([
                'emergencyDetails' => function ($query) {
                    $query->whereIn('type', ['action', 'contact'])
                        ->orderByRaw("FIELD(type, 'action', 'contact')");
                }
            ])->find($id);

            if (!$sosCategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            // Prepare the data in the desired format
            if ($sosCategory) {
                // Separate actions and contacts
                $actions = $sosCategory->emergencyDetails->where('type', 'action')->values();
                $contacts = $sosCategory->emergencyDetails->where('type', 'contact')->values();

                // Prepare the data
                $sosCategoryData = [
                    'id' => $sosCategory->id,
                    'name' => $sosCategory->name,
                    'image' => $sosCategory->image,
                    'emergency_details' => [
                        'actions' => $actions,   // Filtered actions
                        'contacts' => $contacts  // Filtered contacts
                    ]
                ];
            } else {
                $sosCategoryData = null; // Handle the case where the category is not found
            }

            _dLog(eventType: 'info', activityName: 'SOS Category Edit Accessed', description: 'Accessing edit page for sos category ', modelType: 'SosCategory', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $sosCategoryData,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'SOS Category Edit Error', description: 'Exception during sos category edit retrieval: ' . $e->getMessage(), modelType: 'SosCategory', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'SOS Category Update Started', description: 'Starting the process of update sos category', modelType: 'SosCategory', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'image' => 'sometimes|image|mimes:png,jpg,jpeg|max:5120',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'SOS Category Update Validation Failed', description: 'Validation error during sos category update', modelType: 'SosCategory', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $sosCategory = SosCategory::find($id);
            if (!$sosCategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            ini_set('upload_max_filesize', '5M');
            ini_set('post_max_size', '5M');

            if ($request->hasFile('image') && $request->hasFile('image') != null) {
                $path = $request->file('image')->store('sos_categories', 'public');

                $oldImg = $sosCategory->image;

                if ($oldImg && Storage::disk('public')->exists($oldImg)) {
                    Storage::disk('public')->delete($oldImg);
                }

                $sosCategory->image = $path;
            }

            $sosCategory->name = $request->title;
            $sosCategory->save();

            $selectedSociety = getSelectedSociety($request);
            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            // ------------------ Handle Deletion of Old Records ------------------
            // Retrieve all existing IDs for this SOS Category
            $existingIds = SosCategoryEmergencyDetail::where('sos_category_id', $sosCategory->id)->where('society_id', $selectedSociety)->pluck('id')->toArray();

            // Get received IDs from the request (action and emergency contact IDs)
            $receivedActionIds = $request->input('action_id', []); // array
            $receivedEmergencyIds = $request->input('em_id', []); // array
            $receivedIds = array_merge($receivedActionIds, $receivedEmergencyIds);

            // Find IDs to delete (those in $existingIds but not in $receivedIds)
            $idsToDelete = array_diff($existingIds, $receivedIds);

            // Delete records whose IDs are not in the received list
            if (!empty($idsToDelete)) {
                SosCategoryEmergencyDetail::whereIn('id', $idsToDelete)->delete();
            }

            // ------------------ Update/Insert Actions ------------------
            $selectedSociety = getSelectedSociety($request);
            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            // Actions update
            $actionNames = $request->input('action_name'); // array
            foreach ($actionNames as $key => $actionName) {
                // Check if the action name is not empty
                if (!empty($actionName)) {
                    $actionId = $request->input('action_id.' . $key); // Get the action id
                    if ($actionId) {
                        // If action id exists, update the record
                        $emergencyDetail = SosCategoryEmergencyDetail::find($actionId);
                        if ($emergencyDetail) {
                            $emergencyDetail->name = $actionName;
                            $emergencyDetail->type = 'action';
                            $emergencyDetail->society_id = $selectedSociety;
                            $emergencyDetail->save();
                        }
                    } else {
                        // If action id doesn't exist, create a new record
                        $emergencyDetail = new SosCategoryEmergencyDetail();
                        $emergencyDetail->name = $actionName;
                        $emergencyDetail->type = 'action';
                        $emergencyDetail->sos_category_id = $sosCategory->id;
                        $emergencyDetail->society_id = $selectedSociety;
                        $emergencyDetail->save();
                    }
                }
            }

            // ------------------ Update/Insert Emergency Contacts ------------------
            $emerNames = $request->input('em_name'); // array
            $emerPhone = $request->input('em_phone'); // array
            foreach ($emerNames as $key => $emerName) {
                if (!empty($emerName)) {
                    $emergencyId = $request->input('em_id.' . $key); // Get the emergency contact id
                    if ($emergencyId) {
                        // If emergency contact id exists, update the record
                        $emergencyDetail = SosCategoryEmergencyDetail::find($emergencyId);
                        if ($emergencyDetail) {
                            $emergencyDetail->name = $emerName;
                            $emergencyDetail->phone = $emerPhone[$key];
                            $emergencyDetail->type = 'contact';
                            $emergencyDetail->society_id = $selectedSociety;
                            $emergencyDetail->save();
                        }
                    } else {
                        // If emergency contact id doesn't exist, create a new record
                        $emergencyDetail = new SosCategoryEmergencyDetail();
                        $emergencyDetail->name = $emerName;
                        $emergencyDetail->phone = $emerPhone[$key];
                        $emergencyDetail->type = 'contact';
                        $emergencyDetail->sos_category_id = $sosCategory->id;
                        $emergencyDetail->society_id = $selectedSociety;
                        $emergencyDetail->save();
                    }
                }
            }


            DB::commit();

            _dLog(eventType: 'info', activityName: 'SOS Category Updated', description: 'SOS Category updated ( Title : ' . $sosCategory->title . ' ) ', modelType: 'SosCategory', modelId: $sosCategory->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $sosCategory->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'SOS Category updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'SOS Category Update Failed', description: 'Exception during sos category update: ' . $e->getMessage(), modelType: 'SosCategory', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'SOS Category Deletion Started', description: 'Starting the process of deleting ', modelType: 'SosCategory', modelId: $id);

            DB::beginTransaction();
            $sosCategory = SosCategory::find($id);
            if (!$sosCategory) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $sosCategory->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'SOS Category Deleted', description: 'SOS Category deleted ( Title : ' . $sosCategory->title . ' ) ', modelType: 'SosCategory', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'SOS Category Deletion Failed', description: 'Exception during sos category deletion: ' . $e->getMessage(), modelType: 'SosCategory', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
