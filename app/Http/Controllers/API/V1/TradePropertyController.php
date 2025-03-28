<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Models\Bhk;
use App\Models\Block;
use App\Models\TradePropertiesFile;
use App\Models\TradeProperty;
use App\Models\User;
use App\Notifications\DynamicNotification;
use Illuminate\Http\Request;
use App\Models\Society;

class TradePropertyController extends Controller
{

    public function elements()
    {
        try {
            $society_id = auth()->user()->society_id;
            $society_q = Society::with('blocks')
                ->searchById($society_id);
            $societies = $society_q->get();
            $society = $societies->first();
            $bhks = Bhk::all();

            // return response()->json($society);
            // Check if not found
            if (is_null($society) || $bhks->isEmpty()) {
                return res(
                    status: false,
                    message: "Resources are not found!",
                    code: HTTP_OK
                );
            }
            // $blocks = $society->blocks;
            $blocks = Block::where('society_id', $society_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->groupBy('name')
                ->map(function ($group) {
                    return $group->first(); // Return the first block in each group
                })
                ->values(); // Reset the keys to get a clean array
            $totalFloors = $society->floors;
            $numberOfFloors = range(1, $totalFloors);
            $numberOfFloors = array_map(function ($floor) {
                return ['name' => $floor];
            }, $numberOfFloors);


            $amenities = [
                ["name" => "Gym"],
                ["name" => "Swimming"],
                ["name" => "Clubhouse"],
                ["name" => "Garden"],
                // ["name" => "Walking"],
                ["name" => "Playgound"],
                // ["name" => "Tennis"],
                // ["name" => "Badminton"],
                // ["name" => "Basketball"],
                // ["name" => "Indoor"],
                // ["name" => "Community"],
                ["name" => "Yoga"],
                // ["name" => "CCTV"],
                // ["name" => "Security"],
                // ["name" => "Power"],
                // ["name" => "Intercom"],
                // ["name" => "Lifts"],
                // ["name" => "Rainwater"],
                // ["name" => "Fire"],
                // ["name" => "Amphitheater"],
                // ["name" => "Skating"],
                ["name" => "Cafeteria"],
                ["name" => "Library"],
                ["name" => "Theatre"],
                ["name" => "Spa"],
                ["name" => "ATM"],
                ["name" => "Internet"],
                ["name" => "Grocery"],
                // ["name" => "Solar"],
                ["name" => "Parking"],
                // ["name" => "Covered"],
                // ["name" => "Pet"],
                // ["name" => "Barbecue"],
                // ["name" => "Business"],
                // ["name" => "Waste"],
                ["name" => "Laundry"],
                // ["name" => "Car"],
                // ["name" => "Cycling"],
                // ["name" => "Cricket"],
                // ["name" => "Football"],
                // ["name" => "Volleyball"],
                // ["name" => "Billiards"],
            ];

            $unitType = [
                ["name" => "Flat"],
                ["name" => "Plot"],
                ["name" => "Office"],
                ["name" => "Other"],
            ];

            $data = [
                'blocks' => $blocks,
                'floors' => $numberOfFloors,
                'unit_type' => $unitType,
                'bhks' => $bhks,
                'amenities' => $amenities,
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

    public function storeRent(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'block_id' => 'required|exists:blocks,id',
                'floor' => 'required|integer',
                'unit_type' => 'required|string|max:255',
                'unit_number' => 'required|string|max:255',
                'bhk' => 'required|string',
                'area' => 'required|numeric',
                'rent_per_month' => 'required|numeric',
                'security_deposit' => 'required|numeric',
                'available_from_date' => 'required|date',
                'amenities' => 'required|array',
                'files' => 'sometimes|array|max:5', // Ensure max 5 files
                'files.*' => 'image|mimes:jpg,jpeg,png|max:5120', // Validate image files
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $created_by = auth()->id();
            $society_id = auth()->user()->society_id;

            // Create Document data only
            $tradeProperty = TradeProperty::create([
                ...$request->all(),
                'type' => 'rent',
                'created_by' => $created_by,
                'society_id' => $society_id,
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('property_images', 'public');
                    TradePropertiesFile::create([
                        'trade_property_id' => $tradeProperty->id,
                        'file' => $filePath
                    ]);
                }
            }

            $tradeProperty = TradeProperty::with('society', 'user')->find($tradeProperty->id);

            // ================================================
            // Send notifications to Super Admin & Admin
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'property_listing.';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($tradeProperty) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $tradeProperty->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);
            $checkSettings = 'property_related_notifications';
            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'New Rental Property Listed',
                            'body' => $tradeProperty->user->name . "  has listed a property for rent. Please review the listing details.",
                            'model' => 'TradeProperty',
                            'model_id' => $tradeProperty->id,
                            'society_name' => $tradeProperty->society->name,
                            'society_id' => $tradeProperty->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================
            // send to all app users whose have enabled sett.
            User::whereHas('notificationSettings', function ($query) use ($checkSettings, $tradeProperty) {
                $query->where('name', $checkSettings)
                    ->where('status', 'enabled')
                    ->where('user_id', '!=', $tradeProperty->created_by)
                    ->where('user_of_system', 'app')
                    ->where('society_id', $tradeProperty->society_id);
            })
                ->chunk(200, function ($users) use ($tradeProperty) {
                    foreach ($users as $notifyUser) {
                        if ($notifyUser->device_id) {
                            $deviceId = $notifyUser->device_id;
                            $notificationMessageArray = [
                                'title' => 'New Rental Property Listed',
                                'body' => $tradeProperty->user->name . "  has listed a property for rent. Please review the listing details.",
                            ];

                            // Dispatch the job to send the push notification asynchronously
                            SendPushNotificationJob::dispatch($notifyUser->id, $deviceId, $notificationMessageArray);
                        }
                    }
                });

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            \DB::commit();
            $data = [
                'tradeProperty' => $tradeProperty,
            ];

            return res(
                status: true,
                message: "Successfully created",
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

    public function storeSell(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'block_id' => 'required|exists:blocks,id',
                'floor' => 'required|integer',
                'unit_type' => 'required|string|max:255',
                'unit_number' => 'required|string|max:255',
                'bhk' => 'required|string',
                'area' => 'required|numeric',
                'house_price' => 'required|numeric',
                'upfront' => 'required|numeric',
                'available_from_date' => 'required|date',
                'amenities' => 'required|array',
                'files' => 'sometimes|array|max:5', // Ensure max 5 files
                'files.*' => 'image|mimes:jpg,jpeg,png|max:5120', // Validate image files
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $created_by = auth()->id();
            $society_id = auth()->user()->society_id;

            // Create Document data only
            $tradeProperty = TradeProperty::create([
                ...$request->all(),
                'type' => 'sell',
                'created_by' => $created_by,
                'society_id' => $society_id,
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('property_images', 'public');
                    TradePropertiesFile::create([
                        'trade_property_id' => $tradeProperty->id,
                        'file' => $filePath
                    ]);
                }
            }


            $tradeProperty = TradeProperty::with('society', 'user')->find($tradeProperty->id);

            // ================================================
            // Send notifications to Super Admin & Admin
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'property_listing.';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($tradeProperty) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $tradeProperty->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);
            $checkSettings = 'property_related_notifications';

            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'Property Listed for Sale',
                            'body' => $tradeProperty->user->name . "  has listed a property for sale. Please review the listing details.",
                            'model' => 'TradeProperty',
                            'model_id' => $tradeProperty->id,
                            'society_name' => $tradeProperty->society->name,
                            'society_id' => $tradeProperty->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================
            // send to all app users whose have enabled sett.
            User::whereHas('notificationSettings', function ($query) use ($checkSettings, $tradeProperty) {
                $query->where('name', $checkSettings)
                    ->where('status', 'enabled')
                    ->where('user_id', '!=', $tradeProperty->created_by)
                    ->where('user_of_system', 'app')
                    ->where('society_id', $tradeProperty->society_id);
            })
                ->chunk(200, function ($users) use ($tradeProperty) {
                    foreach ($users as $notifyUser) {
                        if ($notifyUser->device_id) {
                            $deviceId = $notifyUser->device_id;
                            $notificationMessageArray = [
                                'title' => 'Property Listed for Sale',
                                'body' => $tradeProperty->user->name . "  has listed a property for sale. Please review the listing details.",
                            ];

                            // Dispatch the job to send the push notification asynchronously
                            SendPushNotificationJob::dispatch($notifyUser->id, $deviceId, $notificationMessageArray);
                        }
                    }
                });

            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            \DB::commit();
            $data = [
                'tradeProperty' => $tradeProperty,
            ];

            return res(
                status: true,
                message: "Successfully created",
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

    public function myRentSellList(Request $request)
    {
        try {
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;

            // Start building the query
            // $query = TradeProperty::with('files')
            //     ->searchByResident($user_id)
            //     ->searchBySocietyId($society_id);

            $query = TradeProperty::with('files')
                ->select('trade_properties.*', 'blocks.name as block_name')
                ->join('blocks', 'trade_properties.block_id', '=', 'blocks.id')
                ->where('trade_properties.created_by', $user_id)  // Explicitly specify the table for created_by
                ->where('trade_properties.society_id', $society_id);

            if ($request->has('type')) {

                $query = $query->searchByType($request->type);
            }

            // Order by created_at date
            $properties = $query->orderBy('created_at', 'desc')->paginate(25);

            // Check if properties are found
            if ($properties->isEmpty()) {
                return res(
                    status: false,
                    message: "No properties found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'properties' => $properties,
            ];

            return res(
                status: true,
                message: "Properties retrieved successfully",
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

    public function detailsRentSell($id)
    {
        try {
            $society_id = auth()->user()->society_id;

            // Start building the query to fetch the trade property with its associated files
            $query = TradeProperty::with('files')
                ->select('trade_properties.*', 'blocks.name as block_name')
                ->join('blocks', 'trade_properties.block_id', '=', 'blocks.id')
                // ->searchById($id)
                // ->searchBySocietyId($society_id);
                ->where('trade_properties.id', $id)  // Explicitly specify the table for created_by
                ->where('trade_properties.society_id', $society_id);

            $property = $query->get();

            // Check if the property is found
            if ($property->isEmpty()) {
                return res(
                    status: false,
                    message: "No property found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'property' => $property,
            ];

            return res(
                status: true,
                message: "Property retrieved successfully",
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


    public function mySocietiesOthersRentSellList(Request $request)
    {
        try {
            $user_id = auth()->id();
            $society_id = auth()->user()->society_id;

            // Start building the query
            // $query = TradeProperty::with('files')
            //     ->searchByOtherResidentExceptMe($user_id)
            //     ->searchBySocietyId($society_id);

            $query = TradeProperty::with('files')
                ->select('trade_properties.*', 'blocks.name as block_name')
                ->join('blocks', 'trade_properties.block_id', '=', 'blocks.id')
                ->where('trade_properties.created_by', '!=', $user_id)  // Explicitly specify the table for created_by
                ->where('trade_properties.society_id', $society_id);

            if ($request->has('type')) {

                $query = $query->searchByType($request->type);
            }

            // Order by created_at date
            $properties = $query->orderBy('created_at', 'desc')->paginate(25);

            // Check if properties are found
            if ($properties->isEmpty()) {
                return res(
                    status: false,
                    message: "No properties found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'properties' => $properties,
            ];

            return res(
                status: true,
                message: "Properties retrieved successfully",
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

    public function updateSell(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'block_id' => 'required|exists:blocks,id',
                'floor' => 'required|integer',
                'unit_type' => 'required|string|max:255',
                'unit_number' => 'required|string|max:255',
                'bhk' => 'required|string',
                'area' => 'required|numeric',
                'house_price' => 'required|numeric',
                'upfront' => 'required|numeric',
                'available_from_date' => 'required|date',
                'amenities' => 'required|array',
                'files' => 'sometimes|array|max:5', // Ensure max 5 files
                'files.*' => 'image|mimes:jpg,jpeg,png|max:5120', // Validate image files
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $created_by = auth()->id();
            $society_id = auth()->user()->society_id;

            // Find the property by ID and ensure it belongs to the user and their society
            $tradeProperty = TradeProperty::where('id', $id)
                ->where('created_by', $created_by)
                ->where('society_id', $society_id)
                ->with('files') // Include related files
                ->first();

            if (!$tradeProperty) {
                return res(
                    status: false,
                    message: "Property not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Update the property data
            $tradeProperty->update([
                ...$request->all(),
                'type' => 'sell', // Ensure the type remains 'sell'
            ]);

            // Handle file uploads (remove old ones and add new ones)
            // if ($request->hasFile('files')) {
            // Delete old files only if new files are being provided
            if (!empty($request->file('files'))) {
                // Delete old files
                $tradeProperty->files()->delete();

                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('property_images', 'public');
                    TradePropertiesFile::create([
                        'trade_property_id' => $tradeProperty->id,
                        'file' => $filePath
                    ]);
                }
            } else {
                // If files array is provided but empty, delete old files
                $tradeProperty->files()->delete();
            }
            // }

            \DB::commit();

            // Reload the trade property with updated file relations
            $tradeProperty->load('files');

            $data = [
                'tradeProperty' => $tradeProperty,
            ];

            return res(
                status: true,
                message: "Successfully updated",
                data: $data,
                code: HTTP_OK
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


    public function updateRent(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'block_id' => 'required|exists:blocks,id',
                'floor' => 'required|integer',
                'unit_type' => 'required|string|max:255',
                'unit_number' => 'required|string|max:255',
                'bhk' => 'required|string',
                'area' => 'required|numeric',
                'rent_per_month' => 'required|numeric',
                'security_deposit' => 'required|numeric',
                'available_from_date' => 'required|date',
                'amenities' => 'required|array',
                'files' => 'sometimes|array|max:5', // Ensure max 5 files
                'files.*' => 'image|mimes:jpg,jpeg,png|max:5120', // Validate image files
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $created_by = auth()->id();
            $society_id = auth()->user()->society_id;

            // Find the property by ID and ensure it belongs to the user and their society
            $tradeProperty = TradeProperty::where('id', $id)
                ->where('created_by', $created_by)
                ->where('society_id', $society_id)
                ->with('files') // Include related files
                ->first();

            if (!$tradeProperty) {
                return res(
                    status: false,
                    message: "Property not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Update the property data
            $tradeProperty->update([
                ...$request->all(),
                'type' => 'rent', // Ensure the type remains 'rent'
            ]);

            // Handle file uploads (remove old ones and add new ones)
            // if ($request->hasFile('files')) {
            // Delete old files only if new files are being provided
            if (!empty($request->file('files'))) {
                // Delete old files
                $tradeProperty->files()->delete();

                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('property_images', 'public');
                    TradePropertiesFile::create([
                        'trade_property_id' => $tradeProperty->id,
                        'file' => $filePath
                    ]);
                }
            } else {
                // If files array is provided but empty, delete old files
                $tradeProperty->files()->delete();
            }
            // }

            \DB::commit();

            // Reload the trade property with updated file relations
            $tradeProperty->load('files');

            $data = [
                'tradeProperty' => $tradeProperty,
            ];

            return res(
                status: true,
                message: "Successfully updated",
                data: $data,
                code: HTTP_OK
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

    public function destroy($id)
    {
        \DB::beginTransaction();
        try {
            // Check if the visitor exists
            $listing = TradeProperty::find($id);

            if (!$listing) {
                return res(
                    status: false,
                    message: "Not found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Check user permissions (optional, based on role)
            if (!in_array(auth()->user()->role, ['admin', 'resident']) && $listing->society_id !== auth()->user()->society_id) {
                return res(
                    status: false,
                    message: "You do not have permission to delete",
                    code: HTTP_FORBIDDEN
                );
            }

            // Delete the record
            $listing->delete();

            \DB::commit();

            return res(
                status: true,
                message: "Deleted successfully",
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
