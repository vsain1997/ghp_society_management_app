<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\CallbackRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceProviders;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ServiceProviderController extends Controller
{
    public function getServiceCategories()
    {
        try {
            $service_categories = ServiceCategory::all();
            // Check if not found
            if ($service_categories->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_OK
                );
            }

            $data = [
                'service_categories' => $service_categories,
            ];
            return res(
                status: true,
                message: "Data retrieved successfully",
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

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'service_category_id' => 'required|string',
                'name' => 'required|string',
                'phone' => 'required|string|min:10|max:10',
                'email' => 'required|email',
                'address' => 'required|string|max:255',
                'society_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->role = 'service_provider';
            $user->status = 'active';
            $user->password = Hash::make($request->input('password'));
            $user->save();


            $service_provider = new ServiceProviders();
            $service_provider->name = $request->input('name');
            $service_provider->phone = $request->input('phone');
            $service_provider->email = $request->input('email');
            $service_provider->address = $request->input('address');
            $service_provider->society_id = $request->input('society_id');
            $service_provider->service_category_id = $request->input('service_category_id');
            $service_provider->user_id = $user->id;
            $service_provider->save();


            \DB::commit();
            $data = [
                'service_provider' => $service_provider,
            ];

            return res(
                status: true,
                message: "Created successfully",
                data: $data,
                code: HTTP_CREATED
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getServiceProviders(Request $request)
    {
        try {

            $validator = validator($request->all(), [
                'service_category_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }
            // $user_id = auth()->id();
            $society_id = auth()->user()->society_id;

            // Start building the query
            $query = ServiceProviders::searchBySocietyId($society_id)
                ->searchByStatus('active')
                ->searchByCategory($request->service_category_id);

            $serviceProviders = $query->paginate(25);

            // Check if result are found
            if ($serviceProviders->isEmpty()) {
                return res(
                    status: false,
                    message: "No service provider found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'service_providers' => $serviceProviders,
            ];
            return res(
                status: true,
                message: "Data retrieved successfully",
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

    public function createRequestCallback(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'service_category_id' => 'required|integer|exists:service_categories,id',
                'service_provider_user_id' => 'required|integer|exists:users,id',
                'aprt_no' => 'required|string',
                'description' => 'sometimes|string|nullable',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $request_by = auth()->user()->id;
            $society_id = auth()->user()->society_id;

            $callback_request = new CallbackRequest();
            $callback_request->service_category_id = $request->input('service_category_id');
            $callback_request->request_by = $request_by;
            $callback_request->request_to = $request->input('service_provider_user_id');
            $callback_request->society_id = $society_id;
            $callback_request->aprt_no = $request->input('aprt_no');
            $callback_request->description = $request->input('description');
            $callback_request->status = 'requested';
            $callback_request->save();

            \DB::commit();
            $data = [
                'callback_request' => $callback_request,
            ];

            return res(
                status: true,
                message: "Created successfully",
                data: $data,
                code: HTTP_CREATED
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
