<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;
use Spatie\Permission\Models\Permission;

class ServiceProviderCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:service_provider_category.view|service_provider_category.create|service_provider_category.edit|service_provider_category.delete')->only(['index', 'show']);
        $this->middleware('permission:service_provider_category.create')->only(['store']);
        $this->middleware('permission:service_provider_category.edit')->only(['edit', 'update']);
        $this->middleware('permission:service_provider_category.delete')->only(['destroy']);

    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Category Index Accessed', description: 'Accessing service provider category index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            // $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'name');

            $sproviderCategorys = ServiceCategory::when($search && $search_col, function ($query) use ($search, $search_col) {
                return $query->where($search_col, 'LIKE', '%' . $search . '%');
            })
                ->orderBy('id', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Service Provider Category Retrieved', description: 'Service Provider Category retrieved', status: 'success', severityLevel: 1);

            return view('admin::serviceProviderCategory.serviceProviderCategory', [
                'sproviderCategory' => $sproviderCategorys,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Service Provider Category Index Error', description: 'Exception during service provider category index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Category Creation Started', description: 'Starting the process of creating a new service provider category');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Service Provider Category Creation Validation Failed', description: 'Validation error during service provider category creation: ' . $validator->errors()->first(), modelType: 'ServiceCategory', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Create service provider category
            ini_set('upload_max_filesize', '5M');
            ini_set('post_max_size', '5M');
            $path = $request->file('image')->store('service_categories', 'public');
            $sproviderCategory = new ServiceCategory();
            $sproviderCategory->name = $request->input('title');
            $sproviderCategory->image = $path;
            $sproviderCategory->save();

            _dLog(eventType: 'info', activityName: 'Service Provider Category Created', description: 'New service provider category created', modelType: 'ServiceCategory', modelId: $sproviderCategory->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $sproviderCategory->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Service Category added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Service Provider Category Creation Failed', description: 'Exception during service provider category creation: ' . $e->getMessage(), modelType: 'ServiceCategory', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $sproviderCategory = ServiceCategory::find($id);

            if (!$sproviderCategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Service Provider Category Edit Accessed', description: 'Accessing edit page for service provider category ', modelType: 'ServiceCategory', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $sproviderCategory,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Service Provider Category Edit Error', description: 'Exception during service provider category edit retrieval: ' . $e->getMessage(), modelType: 'ServiceCategory', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Category Update Started', description: 'Starting the process of update service provider category', modelType: 'ServiceCategory', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'image' => 'sometimes|image|mimes:png,jpg,jpeg|max:5120',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Service Provider Category Update Validation Failed', description: 'Validation error during service provider category update', modelType: 'ServiceCategory', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $sproviderCategory = ServiceCategory::find($id);
            if (!$sproviderCategory) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            ini_set('upload_max_filesize', '5M');
            ini_set('post_max_size', '5M');

            if ($request->hasFile('image') && $request->hasFile('image') != null) {
                $path = $request->file('image')->store('service_categories', 'public');

                $oldImg = $sproviderCategory->image;

                if ($oldImg && Storage::disk('public')->exists($oldImg)) {
                    Storage::disk('public')->delete($oldImg);
                }

                $sproviderCategory->image = $path;
            }

            $sproviderCategory->name = $request->title;
            $sproviderCategory->save();

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Service Provider Category Updated', description: 'Service Provider Category updated ( Title : ' . $sproviderCategory->title . ' ) ', modelType: 'ServiceCategory', modelId: $sproviderCategory->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $sproviderCategory->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Service Category updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Service Provider Category Update Failed', description: 'Exception during service provider category update: ' . $e->getMessage(), modelType: 'ServiceCategory', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'Service Provider Category Deletion Started', description: 'Starting the process of deleting ', modelType: 'ServiceCategory', modelId: $id);

            DB::beginTransaction();
            $sproviderCategory = ServiceCategory::find($id);
            if (!$sproviderCategory) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $sproviderCategory->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Service Provider Category Deleted', description: 'Service Provider Category deleted ( Title : ' . $sproviderCategory->title . ' ) ', modelType: 'ServiceCategory', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Service Provider Category Deletion Failed', description: 'Exception during service provider category deletion: ' . $e->getMessage(), modelType: 'ServiceCategory', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
