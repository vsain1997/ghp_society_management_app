<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplaintCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class ComplaintsCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Complaints Category Index Accessed', description: 'Accessing complaints category index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'name');

            $complaintscategorys = ComplaintCategory::
                when($search && $search_col, function ($query) use ($search, $search_col) {
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Complaints Category Retrieved', description: 'Complaints Categorys retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::complaintsCategory.complaintsCategory', [
                'complaintsCategory' => $complaintscategorys,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Complaints Category Index Error', description: 'Exception during complaints category index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Complaints Category Started', description: 'Starting the process of creating a new complaints category');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Complaints Category Creation Validation Failed', description: 'Validation error during complaints category creation: ' . $validator->errors()->first(), modelType: 'ComplaintCategory', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create complaints category
            $ComplaintCategory = new ComplaintCategory();
            $ComplaintCategory->name = $request->input('title');
            $ComplaintCategory->save();

            _dLog(eventType: 'info', activityName: 'Complaints Category Created', description: 'New complaints category created', modelType: 'ComplaintCategory', modelId: $ComplaintCategory->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $ComplaintCategory->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Complaints Category added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Complaints Category Creation Failed', description: 'Exception during complaints category creation: ' . $e->getMessage(), modelType: 'ComplaintCategory', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $ComplaintCategory = ComplaintCategory::with('createdBy', 'society')->find($id);

            if (!$ComplaintCategory) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }


            _dLog(eventType: 'info', activityName: 'Complaints Category Details Accessed', description: 'Accessing details of complaints category ', modelType: 'ComplaintCategory', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::complaints category.details',
                compact('complaints category')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Complaints Category Details Error', description: 'Exception during complaints category details retrieval: ' . $e->getMessage(), modelType: 'ComplaintCategory', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $complaintscategory = ComplaintCategory::find($id);

            if (!$complaintscategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Complaints Category Edit Accessed', description: 'Accessing edit page for complaints category ', modelType: 'ComplaintCategory', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $complaintscategory,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Complaints Category Edit Error', description: 'Exception during complaints category edit retrieval: ' . $e->getMessage(), modelType: 'ComplaintCategory', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Complaints Category Update Started', description: 'Starting the process of update complaints category', modelType: 'ComplaintCategory', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string'
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Complaints Category Update Validation Failed', description: 'Validation error during complaints category update', modelType: 'ComplaintCategory', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $ComplaintCategory = ComplaintCategory::find($id);
            if (!$ComplaintCategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            $ComplaintCategory->name = $request->title;
            $ComplaintCategory->save();

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Complaints Category Updated', description: 'Complaints Category updated ( Title : ' . $ComplaintCategory->title . ' ) ', modelType: 'ComplaintCategory', modelId: $ComplaintCategory->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $ComplaintCategory->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Complaints Category updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Complaints Category Update Failed', description: 'Exception during complaints category update: ' . $e->getMessage(), modelType: 'ComplaintCategory', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'Complaints Category Deletion Started', description: 'Starting the process of deleting ', modelType: 'ComplaintCategory', modelId: $id);

            DB::beginTransaction();
            $ComplaintCategory = ComplaintCategory::find($id);
            if (!$ComplaintCategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }
            $ComplaintCategory->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Complaints Category Deleted', description: 'Complaints Category deleted ( Title : ' . $ComplaintCategory->title . ' ) ', modelType: 'ComplaintCategory', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Complaints Category Deletion Failed', description: 'Exception during complaints category deletion: ' . $e->getMessage(), modelType: 'ComplaintCategory', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
