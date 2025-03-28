<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Bhk;
use App\Models\ReferProperty;
use App\Models\User;
use App\Notifications\DynamicNotification;
use Illuminate\Http\Request;

class ReferPropertyController extends Controller
{
    public function elementsl()
    {
        try {
            $bhks = Bhk::all();
            // Check if not found
            if ($bhks->isEmpty()) {
                return res(
                    status: false,
                    message: "Resources are not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Minimum budget values without brackets
            $minBudgetOptions = [
                '500000' => '5 Lakhs',
                '1000000' => '10 Lakhs',
                '2000000' => '20 Lakhs',
                '3000000' => '30 Lakhs',
                '4000000' => '40 Lakhs',
                '5000000' => '50 Lakhs',
            ];

            // Maximum budget values without brackets
            $maxBudgetOptions = [
                '1000000' => '10 Lakhs',
                '2000000' => '20 Lakhs',
                '3000000' => '30 Lakhs',
                '4000000' => '40 Lakhs',
                '5000000' => '50 Lakhs',
                '10000000' => '1 Crore',
                '20000000' => '2 Crores',
                '50000000' => '5 Crores',
                '100000000' => '10 Crores',
                'max' => 'Above 10 Crores',
            ];

            $unitTypes = [
                'Flat' => 'Flat',
                'Office' => 'Office',
                'Plot' => 'Plot',
                'Other' => 'Other',
            ];

            $propertyStatus = [
                'New Launch' => 'New Launch',
                'Ready to Move' => 'Ready to Move',
                'Under Construction' => 'Under Construction',
                'Resale' => 'Resale',
                'Sold Out' => 'Sold Out',
                'Upcoming' => 'Upcoming',
            ];

            $propertyFencing = [
                'East' => 'East',
                'West' => 'West',
                'North' => 'North',
                'South' => 'South',
                'North-East' => 'North-East',
                'North-West' => 'North-West',
                'South-East' => 'South-East',
                'South-West' => 'South-West',
            ];

            $data = [
                'minBudgetOptions' => $minBudgetOptions,
                'maxBudgetOptions' => $maxBudgetOptions,
                'bhks' => $bhks,
                'unitTypes' => $unitTypes,
                'propertyStatus' => $propertyStatus,
                'propertyFencing' => $propertyFencing,
            ];
            return res(
                status: true,
                message: "Form dropdowns retrieved successfully",
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

    public function elements()
    {
        try {
            $bhks = Bhk::all();

            if ($bhks->isEmpty()) {
                return res(
                    status: false,
                    message: "Resources are not found!",
                    code: HTTP_NOT_FOUND
                );
            }
            // Minimum budget values as separate objects with 'name' => key
            $minBudgetOptions = [
                ['name' => '500000'],
                ['name' => '1000000'],
                ['name' => '2000000'],
                ['name' => '3000000'],
                ['name' => '4000000'],
                ['name' => '5000000'],
            ];

            // Maximum budget values as separate objects with 'name' => key
            $maxBudgetOptions = [
                ['name' => '1000000'],
                ['name' => '2000000'],
                ['name' => '3000000'],
                ['name' => '4000000'],
                ['name' => '5000000'],
                ['name' => '10000000'],
                ['name' => '20000000'],
                ['name' => '50000000'],
                ['name' => '100000000'],
            ];

            // Unit types as separate objects with 'name' => key
            $unitTypes = [
                ['name' => 'resident'],
                ['name' => 'office'],
            ];

            // Property status as separate objects with 'name' => key
            $propertyStatus = [
                ['name' => 'New Launch'],
                ['name' => 'Ready to Move'],
                ['name' => 'Under Construction'],
                ['name' => 'Resale'],
                ['name' => 'Sold Out'],
                ['name' => 'Upcoming'],
            ];

            // Property fencing as separate objects with 'name' => key
            $propertyFencing = [
                ['name' => 'East'],
                ['name' => 'West'],
                ['name' => 'North'],
                ['name' => 'South'],
                ['name' => 'North-East'],
                ['name' => 'North-West'],
                ['name' => 'South-East'],
                ['name' => 'South-West'],
            ];

            $data = [
                'minBudgetOptions' => $minBudgetOptions,
                'maxBudgetOptions' => $maxBudgetOptions,
                'bhks' => $bhks,
                'unitTypes' => $unitTypes,
                'propertyStatus' => $propertyStatus,
                'propertyFencing' => $propertyFencing,
            ];

            return res(
                status: true,
                message: "Form dropdowns retrieved successfully",
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
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:10',
                'min_budget' => 'required|numeric',
                'max_budget' => 'required|numeric',
                'location' => 'required|string|max:255',
                'unit_type' => 'required|string|max:255',
                'bhk' => 'required|string',
                'property_status' => 'required|string|max:255',
                'property_fancing' => 'required|string|max:255',
                'remark' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Create new
            $refer = ReferProperty::create([
                ...$request->all(),
                'user_id' => auth()->id(),
                'society_id' => auth()->user()->society_id,
            ]);

            $refer = ReferProperty::with('user', 'society')->find($refer->id);

            // ================================================
            // Send notifications to Super Admin & Admin
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'refer_property.';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($refer) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $refer->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);

            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                $checkSettings = 'refer_property_notifications';
                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'New Property Referral',
                            'body' => $refer->user->name . " has referred a property. Please review the details and follow up.",
                            'model' => 'ReferProperty',
                            'model_id' => $refer->id,
                            'society_name' => $refer->society->name,
                            'society_id' => $refer->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================

            \DB::commit();
            $data = [
                'refer_property' => $refer,
            ];

            return res(
                status: true,
                message: "Referral property created successfully",
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

    public function index()
    {
        try {
            $user_id = auth()->id();

            // Start building the query
            $query = ReferProperty::searchByResident($user_id);
            // Order by
            $referProperties = $query->orderBy('created_at', 'desc')
                ->paginate(25);

            // Check if result are found
            if ($referProperties->isEmpty()) {
                return res(
                    status: false,
                    message: "No referral properties found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'refer_properties' => $referProperties,
            ];
            return res(
                status: true,
                message: "Referral properties retrieved successfully",
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

    public function details($id)
    {
        try {
            $user_id = auth()->id();

            // Start building the query
            $query = ReferProperty::searchByResident($user_id)
                ->searchById($id);

            $referProperty = $query->get();

            // Check if result are found
            if ($referProperty->isEmpty()) {
                return res(
                    status: false,
                    message: "No referral property found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'refer_property' => $referProperty,
            ];
            return res(
                status: true,
                message: "Referral property details retrieved successfully",
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

    public function update(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:10',
                'min_budget' => 'required|numeric',
                'max_budget' => 'required|numeric',
                'location' => 'required|string|max:255',
                'unit_type' => 'required|string|max:255',
                'bhk' => 'required|string',
                'property_status' => 'required|string|max:255',
                'property_fancing' => 'required|string|max:255',
                'remark' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the existing record
            $refer = ReferProperty::find($id);

            if (!$refer) {
                return res(
                    status: false,
                    message: "Referral property not found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Update record
            $refer->update([
                ...$request->all(),
            ]);

            // Reload with relationships
            $refer = ReferProperty::with('user', 'society')->find($refer->id);

            \DB::commit();
            $data = [
                'refer_property' => $refer,
            ];

            return res(
                status: true,
                message: "Referral property updated successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy($id)
    {
        \DB::beginTransaction();
        try {
            // Find the record
            $refer = ReferProperty::find($id);

            if (!$refer) {
                return res(
                    status: false,
                    message: "Referral property not found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Delete the record
            $refer->delete();
            $refer->forceDelete();

            \DB::commit();

            return res(
                status: true,
                message: "Referral property deleted successfully",
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }



}
