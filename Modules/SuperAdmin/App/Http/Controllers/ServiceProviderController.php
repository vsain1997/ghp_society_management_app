<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Society;
use App\Models\Block;
use App\Models\ServiceProviders;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ServiceProviderController extends Controller
{


    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Index Accessed', description: 'Accessing staff index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: 'name');

            $serviceProviders = ServiceProviders::with('society')
                ->searchByStatus($status)
                ->when($search && $search_col, function ($query) use ($search, $search_col) {
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->where('society_id', $selectedSociety)
                ->orderBy('id', 'desc')
                ->paginate(25);

            $serviceCategories = ServiceCategory::all();

            _dLog(eventType: 'info', activityName: 'Service Providers Retrieved', description: 'Service Providers retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::serviceProvider.serviceProvider', [
                'serviceProviders' => $serviceProviders,
                'serviceCategories' => $serviceCategories,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Service Provider Index Error', description: 'Exception during service providers index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Creation Started', description: 'Starting the process of creating a new staff');

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'category' => 'required|integer',
                'phone' => 'required|string',
                'email' => 'required|email',
                'address' => 'required|string',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Service Provider Creation Validation Failed', description: 'Validation error during service provider creation: ' . $validator->errors()->first(), modelType: 'ServiceProviders', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // user
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = 'service_provider';
            $user->status = 'active';
            $user->password = '';
            $user->save();

            // service provider
            $serviceProvider = new ServiceProviders();
            $serviceProvider->name = $request->input('name');
            $serviceProvider->phone = $request->input('phone');
            $serviceProvider->email = $request->input('email');
            $serviceProvider->address = $request->input('address');
            $serviceProvider->status = 'active';
            $serviceProvider->society_id = $request->input('society_id');
            $serviceProvider->service_category_id = $request->input('category');
            $serviceProvider->user_id = $user->id;
            $serviceProvider->save();

            _dLog(eventType: 'info', activityName: 'Service Provider Created', description: 'New staff created', modelType: 'ServiceProviders', modelId: $serviceProvider->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $serviceProvider->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Service Provider added successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Service Provider Creation Failed', description: 'Exception during service provider creation: ' . $e->getMessage(), modelType: 'ServiceProviders', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $serviceProvider = ServiceProviders::find($id);

            if (!$serviceProvider) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Service Provider Edit Accessed', description: 'Accessing edit page for service provider ', modelType: 'ServiceProviders', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'data' => $serviceProvider,
            ]);
        } catch (Exception $e) {


            _dLog(eventType: 'error', activityName: 'Service Provider Edit Error', description: 'Exception during service provider edit retrieval: ' . $e->getMessage(), modelType: 'ServiceProviders', modelId: null, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Update Started', description: 'Starting the process of update service provider', modelType: 'ServiceProviders', modelId: $id);

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'category' => 'required|integer',
                'phone' => 'required|string',
                'email' => 'required|email',
                'address' => 'required|string',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Service Provider Update Validation Failed', description: 'Validation error during service provider update', modelType: 'ServiceProviders', modelId: $id, status: 'failed');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors(),
                ]);
            }

            $serviceProvider = ServiceProviders::find($id);
            if (!$serviceProvider) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }

            $user = User::find($serviceProvider->user_id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = 'service_provider';
            $user->save();

            // service provider
            $serviceProvider->name = $request->input('name');
            $serviceProvider->phone = $request->input('phone');
            $serviceProvider->email = $request->input('email');
            $serviceProvider->address = $request->input('address');
            $serviceProvider->society_id = $request->input('society_id');
            $serviceProvider->service_category_id = $request->input('category');
            $serviceProvider->save();

            DB::commit();

            _dLog(eventType: 'info', activityName: 'Service Provider Updated', description: 'Service Provider updated ( Name : ' . $serviceProvider->name . ' ) ', modelType: 'ServiceProviders', modelId: $serviceProvider->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $serviceProvider->toArray(), requestData: $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Service Provider updated successfully',
            ]);
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Service Provider Update Failed', description: 'Exception during service provider update: ' . $e->getMessage(), modelType: 'ServiceProviders', modelId: $id, status: 'failed', severityLevel: 2);

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
            _dLog(eventType: 'info', activityName: 'Service Provider Deletion Started', description: 'Starting the process of deleting ', modelType: 'ServiceProviders', modelId: $id);

            DB::beginTransaction();
            $serviceProvider = ServiceProviders::find($id);
            $user = User::find($serviceProvider->user_id);
            if (!$serviceProvider) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found',
                ]);
            }
            $serviceProvider->delete();
            $user->delete();
            DB::commit();

            _dLog(eventType: 'info', activityName: 'Service Provider Deleted', description: 'Service Provider deleted ( Name : ' . $serviceProvider->name . ' ) ', modelType: 'ServiceProviders', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Service Provider Deletion Failed', description: 'Exception during service provider deletion: ' . $e->getMessage(), modelType: 'ServiceProviders', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Service Provider Status Change Started', description: 'Changing status for service provider to ' . $status);

            $serviceProvider = ServiceProviders::find($id);
            if (!$serviceProvider) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }

            $serviceProvider->status = $status;
            $serviceProvider->save();

            $user = User::find($serviceProvider->user_id);
            $user->status = $status;
            $user->save();

            _dLog(eventType: 'info', activityName: 'Service Provider Status Changed', description: 'Service Provider status updated ( Name : ' . $serviceProvider->name . ' ) ', modelType: 'ServiceProviders', modelId: $serviceProvider->id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Service Provider Status Change Failed', description: 'Exception during service provider status change: ' . $e->getMessage(), modelType: 'ServiceProviders', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
