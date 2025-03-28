<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Member;
use App\Models\SocietyContact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Society;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate search input if provided
        $validator = validator($request->all(), [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $query = Society::with('blocks')->where('status', 'active');

            // Filtering by search text
            if ($request->has('search') && $request->search) {
                $searchText = $request->search;
                $query->where(function ($q) use ($searchText) {
                    $q->where('name', 'like', '%' . $searchText . '%')
                        ->orWhere('location', 'like', '%' . $searchText . '%');
                });
            }

            // Pagination
            $societies = $query->paginate(15);

            // Check if societies are found
            if (!$societies) {
                return res(
                    status: false,
                    message: "Societies not found !",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'societies' => $societies,
            ];
            return res(
                status: true,
                message: "Societies retrieved successfully",
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
            $society_id = auth()->user()->society_id;
            $blocks = Block::where('society_id', $society_id)
                ->select('name')
                ->distinct()
                ->orderBy('name')
                ->get();


            $society = Society::where('status', 'active')->where('id', $society_id)
                ->first();
            // Check if not found
            if ($blocks->isEmpty() || !$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $blockArr = array();
            foreach ($blocks as $key => $block) {
                $distinctFloors = Block::where('name', 'like', '%' . $block->name . '%')
                    ->where('society_id', $society_id)
                    ->distinct()
                    ->pluck('floor')
                    ->toArray();
                $distinctFloors = array_map(function ($floor) {
                    return ['name' => $floor];
                }, $distinctFloors);

                $blockCh['name'] = $block->name;
                $blockCh['floors'] = $distinctFloors;
                array_push($blockArr, $blockCh);
            }
            if (empty($blockArr)) {
                $blockCh['name'] = null;
                $blockCh['floors'] = null;
                array_push($blockArr, $blockCh);
            }
            $data = [
                'blocks' => $blockArr,
            ];

            return res(
                status: true,
                message: "Filter dropdowns retrieved successfully",
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

    public function societyContacts()
    {
        try {
            $society_id = auth()->user()->society_id;
            $contacts = SocietyContact::where('society_id', $society_id)
                ->orderBy('name')
                ->get();

            // Check if not found
            if ($contacts->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_OK
                );
            }

            $data = [
                'contacts' => $contacts,
            ];

            return res(
                status: true,
                message: "Contacts retrieved successfully",
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

    public function memberListOld(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_id' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // get total units for this society
            $society = Society::where('status', 'active')->where('id', $society_id)
                ->first();
            // Check if not found
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            // total units are occupied by residence
            $occupied = Member::where('status', 'active')
                ->where('society_id', $society_id)
                ->count();


            // A.also add to below query
            // 1. select members.name, members.aprt_no
            // 2. group by floor_number ,
            // 3. order by floor_number asc
            // $occupied_members = Member::where('status', 'active')
            //     ->where('society_id', $society_id)
            //     ->get();

            // $occupied_members = Member::select('name', 'aprt_no', 'floor_number')
            //     ->where('status', 'active')
            //     ->where('society_id', $society_id)
            //     ->groupBy('floor_number', 'aprt_no', 'name') // Include name and aprt_no in group by to avoid SQL error
            //     ->orderBy('floor_number', 'asc')
            //     ->paginate(50);
            // Get the input for floor_number filter
            $floor_number = $request->input('floor_number');
            $block_id = $request->input('block_id');

            // Start the query
            $query = Member::select('name', 'aprt_no', 'floor_number')
                ->where('status', 'active')
                ->where('society_id', $society_id);

            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_id) {
                $query->where('block_id', $block_id);
            }

            // Group by floor_number, aprt_no, and name
            // $occupied_members = $query->groupBy('floor_number', 'aprt_no', 'name')
            //     ->orderBy('floor_number', 'asc')
            //     ->get();
            // $grouped_members = $occupied_members->groupBy('floor_number');

            // Paginate the results (make sure to apply pagination before grouping)
            $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(50);

            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');


            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Loop through each group of members and restructure the data
            foreach ($grouped_members as $floor_number => $members) {
                $transformed_data[] = [
                    'floor_number' => $floor_number,
                    'vacant_units' => 10, // static value for vacant units
                    'data' => $members->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray(),
                ];
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));



            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                // 'occupied_members' => $occupied_members,
                // 'occupied_members' => $transformed_data,
                'occupied_members' => $occupied_members_paginated,
            ];

            return res(
                status: true,
                message: "Members retrived successfully",
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

    public function memberListOld2(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_id' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            // Get the input for floor_number and block_id filter
            $floor_number = $request->input('floor_number');
            $block_id = $request->input('block_id');

            // Start the query
            $query = Member::select('name', 'aprt_no', 'floor_number')
                ->where('status', 'active')
                ->where('society_id', $society_id);

            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_id) {
                $query->where('block_id', $block_id);
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(50);

            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Loop through each group of members and restructure the data
            foreach ($grouped_members as $floor_number => $members) {
                // Query to get total units for the current floor
                $total_floor_units = (int) $society->floor_units;

                // Count the occupied members on this floor
                $total_floor_occupants = Member::where('society_id', $society_id)
                    ->where('floor_number', $floor_number)
                    ->count();

                // Calculate vacant units
                $vacant_units = $total_floor_units - $total_floor_occupants;

                // Structure the data for this floor
                $transformed_data[] = [
                    'floor_number' => $floor_number,
                    'vacant_units' => $vacant_units,
                    'data' => $members->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray(),
                ];
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'occupied_members' => $occupied_members_paginated, // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function memberListOkButFilterNotWorks(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_id' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units and floors for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
            $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

            // Get the input for floor_number and block_id filter
            $floor_number = $request->input('floor_number');
            $block_id = $request->input('block_id');

            // Start the query
            $query = Member::select('name', 'aprt_no', 'floor_number')
                ->where('status', 'active')
                ->where('society_id', $society_id);

            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_id) {
                $query->where('block_id', $block_id);
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(50);

            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Loop through all possible floors, even if no members are found
            foreach ($allFloors as $floor_number) {
                // Query to get total units for the current floor
                $total_floor_units = (int) $society->floor_units;

                // Count the occupied members on this floor
                $total_floor_occupants = Member::where('society_id', $society_id)
                    ->where('floor_number', $floor_number)
                    ->where('status', 'active')
                    ->count();

                // Calculate vacant units
                $vacant_units = $total_floor_units - $total_floor_occupants;

                // Check if the floor has members and set occupied_members accordingly
                $occupied_members = $grouped_members->has($floor_number)
                    ? $grouped_members[$floor_number]->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray()
                    : []; // Empty array if no members are found for this floor

                // Structure the data for this floor
                $transformed_data[] = [
                    'floor_number' => $floor_number,
                    'vacant_units' => $vacant_units,
                    'occupied_members' => $occupied_members,  // Renamed from 'data' to 'occupied_members'
                ];
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'occupied_members' => $occupied_members_paginated, // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function memberListBlockUnitFixPrev(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_name' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units and floors for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
            $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

            // Get the input for floor_number and block_id filter
            $floor_number = $request->input('floor_number');
            $block_name = $request->input('block_name');

            // Start the query
            $query = Member::select('name', 'aprt_no', 'floor_number')
                ->where('status', 'active')
                ->where('society_id', $society_id);

            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_name) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)
                    ->pluck('id')
                    ->toArray();

                // return $blockIds;

                $query->whereIn('block_id', $blockIds);
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(10);

            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Determine the floors to loop through (filtered floor or all floors)
            $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

            // Loop through the filtered floors or all floors if no filter is applied
            foreach ($floors_to_loop as $floor_number) {
                // Query to get total units for the current floor
                $total_floor_units = (int) $society->floor_units;

                // Count the occupied members on this floor
                $total_floor_occupants = Member::where('society_id', $society_id)
                    ->where('floor_number', $floor_number)
                    ->where('status', 'active')
                    ->count();

                // Calculate vacant units
                $vacant_units = $total_floor_units - $total_floor_occupants;

                // Check if the floor has members and set occupied_members accordingly
                $occupied_members = $grouped_members->has($floor_number)
                    ? $grouped_members[$floor_number]->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray()
                    : []; // Empty array if no members are found for this floor

                // Structure the data for this floor
                $transformed_data[] = [
                    'floor_number' => (int) $floor_number,
                    'total_units' => (int) $total_floor_units,
                    'total_occupants' => (int) $total_floor_occupants,
                    'total_vacants' => $vacant_units,
                    'occupied_members' => $occupied_members,  // Renamed from 'data' to 'occupied_members'
                ];
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'occupied_members' => $occupied_members_paginated, // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function memberListOnlyBlockNamePrev(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_name' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units and floors for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
            $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

            // Get the input for floor_number and block_name filter
            $floor_number = $request->input('floor_number');
            $block_name = $request->input('block_name');

            // Start the query
            $query = Member::select('name', 'aprt_no', 'floor_number', 'block_id')
                ->where('status', 'active')
                ->where('society_id', $society_id);

            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_name) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)  // Ensure this matches the user's society_id
                    ->pluck('id')
                    ->toArray();

                $query->whereIn('block_id', $blockIds);
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(10);

            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Determine the floors to loop through (filtered floor or all floors)
            $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

            // Loop through the filtered floors or all floors if no filter is applied
            foreach ($floors_to_loop as $floor_number) {
                // Query to get total units for the current floor
                $total_floor_units = (int) $society->floor_units;

                // Initialize variables for block calculations
                $total_block_units = 0;
                $total_block_occupants = 0;

                if ($block_name) {
                    // Get the total units for the block
                    $total_block_units = Block::where('id', $blockIds[0])->value('total_units'); // Assuming you have 'total_units' in Block model

                    // Count the occupied members in the block for this floor
                    $total_block_occupants = Member::where('society_id', $society_id)
                        ->where('floor_number', $floor_number)
                        ->whereIn('block_id', $blockIds)
                        ->where('status', 'active')
                        ->count();
                } else {
                    // Count the occupied members on this floor if no block filter is applied
                    $total_block_occupants = Member::where('society_id', $society_id)
                        ->where('floor_number', $floor_number)
                        ->where('status', 'active')
                        ->count();
                }

                // Calculate vacant units
                $vacant_units = $block_name ? ($total_block_units - $total_block_occupants) : ($total_floor_units - $total_block_occupants);

                // Check if the floor has members and set occupied_members accordingly
                $occupied_members = $grouped_members->has($floor_number)
                    ? $grouped_members[$floor_number]->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray()
                    : []; // Empty array if no members are found for this floor

                // Structure the data for this floor
                $transformed_data[] = [
                    'floor_number' => (int) $floor_number,
                    'total_units' => (int) ($block_name ? $total_block_units : $total_floor_units),
                    'total_occupants' => (int) $total_block_occupants,
                    'total_vacants' => $vacant_units,
                    'occupied_members' => $occupied_members,
                ];
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'occupied_members' => $occupied_members_paginated, // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function memberListSearchPrev(Request $request)
    {
        // try {
        // Validate search input if provided
        $validator = validator($request->all(), [
            'block_name' => 'nullable|string|max:255',
            'floor_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $logger = auth()->user();
        $society_id = $logger->society_id;

        // Get total units and floors for this society
        $society = Society::where('status', 'active')->where('id', $society_id)->first();
        if (!$society) {
            return res(
                status: false,
                message: "Not found!",
                code: HTTP_NOT_FOUND
            );
        }

        $total_units = (int) $society->floors * (int) $society->floor_units;
        $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

        $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
        $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

        // Get the input for floor_number and block_name filter
        $floor_number = $request->input('floor_number');
        $block_name = $request->input('block_name');

        // Start the query
        $query = Member::select('name', 'aprt_no', 'floor_number', 'block_id')
            ->where('status', 'active')
            ->where('society_id', $society_id);

        // If block_name is provided and no floor_number, handle only block data
        if ($block_name && !$floor_number) {
            // Get block IDs matching the provided block_name
            $blockIds = Block::where('name', $block_name)
                ->where('society_id', $society_id)  // Ensure this matches the user's society_id
                ->pluck('id')
                ->toArray();

            // Get members for that block
            $query->whereIn('block_id', $blockIds);
        } else {
            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_name) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)  // Ensure this matches the user's society_id
                    ->pluck('id')
                    ->toArray();

                $query->whereIn('block_id', $blockIds);
            }
        }

        // Paginate the results
        $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(10);
        // Group the paginated members by floor_number
        $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');
        // return $grouped_members;

        // Initialize an empty array to store the transformed data
        $transformed_data = [];

        // Handle block-only data if block_name is provided without floor_number
        if ($block_name && !$floor_number) {
            // Get the total units and occupants for the block
            $total_block_units = Block::where('id', $blockIds[0])->value('total_units');
            $total_block_occupants = Member::where('society_id', $society_id)
                ->whereIn('block_id', $blockIds)
                ->where('status', 'active')
                ->count();

            // Calculate vacant units for the block
            $vacant_units = $total_block_units - $total_block_occupants;

            // Collect members of the block
            /*
            $occupied_members = $grouped_members->has($floor_number) ?
                $grouped_members->map(function ($member) {
                    return [
                        'name' => $member->name,
                        'aprt_no' => $member->aprt_no,
                    ];
                })->toArray() : [];
                $occupied_members = $grouped_members->has($floor_number)
                    ? $grouped_members[$floor_number]->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray()
                    : [];
                    */

            $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                return $membersOnBlock->map(function ($member) {
                    return [
                        'name' => $member->name,
                        'aprt_no' => $member->aprt_no,
                    ];
                });
            })->toArray();


            // Structure the response for block data
            $transformed_data[] = [
                'floor_number' => null, // Set floor_number as null
                'total_units' => (int) $total_block_units,
                'total_occupants' => (int) $total_block_occupants,
                'total_vacants' => $vacant_units,
                'occupied_members' => $occupied_members,
            ];

        } else {
            // Loop through the filtered floors or all floors if no filter is applied
            $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

            foreach ($floors_to_loop as $floor_number) {
                // Query to get total units for the current floor
                $total_floor_units = (int) $society->floor_units;

                // Count the occupied members on this floor
                $total_floor_occupants = Member::where('society_id', $society_id)
                    ->where('floor_number', $floor_number)
                    ->where('status', 'active')
                    ->count();

                // Calculate vacant units
                $vacant_units = $total_floor_units - $total_floor_occupants;

                // Structure the data for this floor
                $occupied_members = $grouped_members->has($floor_number)
                    ? $grouped_members[$floor_number]->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    })->toArray()
                    : [];

                $transformed_data[] = [
                    'floor_number' => (int) $floor_number,
                    'total_units' => (int) $total_floor_units,
                    'total_occupants' => (int) $total_floor_occupants,
                    'total_vacants' => $vacant_units,
                    'occupied_members' => $occupied_members,
                ];
            }
        }

        // Update the paginated collection with the transformed data
        $occupied_members_paginated->setCollection(collect($transformed_data));

        $data = [
            'total_units' => $total_units,
            'occupied' => $occupied,
            'occupied_members' => $occupied_members_paginated, // Paginated data with transformed structure
        ];

        return res(
            status: true,
            message: "Members retrieved successfully",
            data: $data,
            code: HTTP_OK
        );

        // } catch (\Exception $e) {
        //     return res(
        //         status: false,
        //         message: $e->getMessage(),
        //         code: HTTP_INTERNAL_SERVER_ERROR
        //     );
        // }
    }

    public function memberListSendMoreKeyOnOccupiedMemberArr(Request $request)
    {
        // Validate search input if provided
        $validator = validator($request->all(), [
            'block_name' => 'nullable|string|max:255',
            'floor_number' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',  // New name filter validation
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $logger = auth()->user();
        $society_id = $logger->society_id;

        // Get total units and floors for this society
        $society = Society::where('status', 'active')->where('id', $society_id)->first();
        if (!$society) {
            return res(
                status: false,
                message: "Not found!",
                code: HTTP_NOT_FOUND
            );
        }

        $total_units = (int) $society->floors * (int) $society->floor_units;
        $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

        $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
        $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

        // Get the input for filters
        $floor_number = $request->input('floor_number');
        $block_name = $request->input('block_name');
        $search = $request->input('search');  // New filter for member name

        // Start the query
        $query = Member::select('name', 'aprt_no', 'floor_number', 'block_id')
            ->where('status', 'active')
            ->where('society_id', $society_id);

        // If block_name is provided and no floor_number, handle only block data
        if ($block_name && !$floor_number) {
            // Get block IDs matching the provided block_name
            $blockIds = Block::where('name', $block_name)
                ->where('society_id', $society_id)
                ->pluck('id')
                ->toArray();

            // Get members for that block
            $query->whereIn('block_id', $blockIds);
        } else {
            // Apply the floor_number filter if it's provided
            if ($floor_number) {
                $query->where('floor_number', $floor_number);
            }
            if ($block_name) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)
                    ->pluck('id')
                    ->toArray();

                $query->whereIn('block_id', $blockIds);
            }
        }

        // Apply the search filter if it's provided
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('aprt_no', 'like', '%' . $search . '%');
            });
        }


        // Paginate the results
        $occupied_members_paginated = $query->orderBy('floor_number', 'asc')->paginate(10);
        // Group the paginated members by floor_number
        $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

        // Initialize an empty array to store the transformed data
        $transformed_data = [];

        // Handle block-only data if block_name is provided without floor_number
        if ($block_name && !$floor_number) {
            // Get the total units and occupants for the block
            $total_block_units = Block::where('id', $blockIds[0])->value('total_units');
            $total_block_occupants = Member::where('society_id', $society_id)
                ->whereIn('block_id', $blockIds)
                ->where('status', 'active')
                ->count();

            // Calculate vacant units for the block
            $vacant_units = $total_block_units - $total_block_occupants;

            // Collect members of the block
            $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                return $membersOnBlock->map(function ($member) {
                    return [
                        'name' => $member->name,
                        'aprt_no' => $member->aprt_no,
                    ];
                });
            })->toArray();

            // Structure the response for block data
            $transformed_data[] = [
                'floor_number' => null,  // Set floor_number as null
                'total_units' => (int) $total_block_units,
                'total_occupants' => (int) $total_block_occupants,
                'total_vacants' => $vacant_units,
                'occupied_members' => $occupied_members,
            ];

        } else {
            // If name filter is applied, set certain keys to null
            if ($search) {
                $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                    return $membersOnBlock->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                        ];
                    });
                })->toArray();

                $transformed_data[] = [
                    'floor_number' => null,  // Set to null since name filter is applied
                    'total_units' => null,    // Set to null since name filter is applied
                    'total_occupants' => null, // Set to null since name filter is applied
                    'total_vacants' => null,   // Set to null since name filter is applied
                    'occupied_members' => $occupied_members,
                ];
            } else {
                // Loop through the filtered floors or all floors if no filter is applied
                $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

                foreach ($floors_to_loop as $floor_number) {
                    // Query to get total units for the current floor
                    $total_floor_units = (int) $society->floor_units;

                    // Count the occupied members on this floor
                    $total_floor_occupants = Member::where('society_id', $society_id)
                        ->where('floor_number', $floor_number)
                        ->where('status', 'active')
                        ->count();

                    // Calculate vacant units
                    $vacant_units = $total_floor_units - $total_floor_occupants;

                    // Structure the data for this floor
                    $occupied_members = $grouped_members->has($floor_number)
                        ? $grouped_members[$floor_number]->map(function ($member) {
                            return [
                                'name' => $member->name,
                                'aprt_no' => $member->aprt_no,
                            ];
                        })->toArray()
                        : [];

                    $transformed_data[] = [
                        'floor_number' => (int) $floor_number,
                        'total_units' => (int) $total_floor_units,
                        'total_occupants' => (int) $total_floor_occupants,
                        'total_vacants' => $vacant_units,
                        'occupied_members' => $occupied_members,
                    ];
                }
            }
        }

        // Update the paginated collection with the transformed data
        $occupied_members_paginated->setCollection(collect($transformed_data));

        $data = [
            'total_units' => $total_units,
            'occupied' => $occupied,
            'occupied_members' => $occupied_members_paginated,  // Paginated data with transformed structure
        ];

        return res(
            status: true,
            message: "Members retrieved successfully",
            data: $data,
            code: HTTP_OK
        );
    }

    // here you are finding when only block_name get from request then
    // in response floor_number is null, but i want floor_number , you will get floor_number from members table members.floor_number (take first member's floor_number from $occupied_members )

    public function memberListWell(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_name' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
                'search' => 'nullable|string|max:255',  // New name filter validation
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units and floors for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
            $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

            // Get the input for filters
            $floor_number = $request->input('floor_number');
            $block_name = $request->input('block_name');
            $search = $request->input('search');  // New filter for member name

            // Start the query
            $query = Member::select('members.name', 'members.aprt_no', 'members.floor_number', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $society_id);

            // If block_name is provided and no floor_number, handle only block data
            if ($block_name && !$floor_number) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)
                    ->pluck('id')
                    ->toArray();

                // Get members for that block
                $query->whereIn('members.block_id', $blockIds);
            } else {
                // Apply the floor_number filter if it's provided
                if ($floor_number) {
                    $query->where('members.floor_number', $floor_number);
                }
                if ($block_name) {
                    $blockIds = Block::where('name', $block_name)
                        ->where('society_id', $society_id)
                        ->pluck('id')
                        ->toArray();

                    $query->whereIn('members.block_id', $blockIds);
                }
            }

            // Apply the search filter if it's provided
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('members.name', 'like', '%' . $search . '%')
                        ->orWhere('members.aprt_no', 'like', '%' . $search . '%');
                });
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('members.floor_number', 'asc')->paginate(10);
            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Handle block-only data if block_name is provided without floor_number
            if ($block_name && !$floor_number) {
                // Get the total units and occupants for the block
                $total_block_units = Block::where('id', $blockIds[0])->value('total_units');
                $total_block_occupants = Member::where('society_id', $society_id)
                    ->whereIn('block_id', $blockIds)
                    ->where('status', 'active')
                    ->count();

                // Calculate vacant units for the block
                $vacant_units = $total_block_units - $total_block_occupants;

                // Collect members of the block
                $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                    return $membersOnBlock->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                            'floor_number' => $member->floor_number,
                            'block_name' => $member->block_name,
                        ];
                    });
                })->toArray();

                if (empty($occupied_members)) {
                    $occupied_members = [
                        [
                            'name' => null,
                            'aprt_no' => null,
                            'floor_number' => null,
                            'block_name' => null,
                        ]
                    ];
                }

                $first_member_floor_number = isset($occupied_members[0]) ? $occupied_members[0]['floor_number'] : null;


                // Structure the response for block data
                $transformed_data[] = [
                    'floor_number' => $first_member_floor_number,  // Set floor_number as null
                    'total_units' => (int) $total_block_units,
                    'total_occupants' => (int) $total_block_occupants,
                    'total_vacants' => $vacant_units,
                    'occupied_members' => $occupied_members,
                ];

            } else {
                // If name filter is applied, set certain keys to null
                if ($search) {
                    $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                        return $membersOnBlock->map(function ($member) {
                            return [
                                'name' => $member->name,
                                'aprt_no' => $member->aprt_no,
                                'floor_number' => $member->floor_number,
                                'block_name' => $member->block_name,
                            ];
                        });
                    })->toArray();

                    if (empty($occupied_members)) {
                        $occupied_members = [
                            [
                                'name' => null,
                                'aprt_no' => null,
                                'floor_number' => null,
                                'block_name' => null,
                            ]
                        ];
                    }

                    $transformed_data[] = [
                        'floor_number' => null,  // Set to null since name filter is applied
                        'total_units' => null,    // Set to null since name filter is applied
                        'total_occupants' => null, // Set to null since name filter is applied
                        'total_vacants' => null,   // Set to null since name filter is applied
                        'occupied_members' => $occupied_members,
                    ];
                } else {
                    // Loop through the filtered floors or all floors if no filter is applied
                    $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

                    foreach ($floors_to_loop as $floor_number) {
                        // Query to get total units for the current floor
                        $total_floor_units = (int) $society->floor_units;

                        // Count the occupied members on this floor
                        $total_floor_occupants = Member::where('society_id', $society_id)
                            ->where('floor_number', $floor_number)
                            ->where('status', 'active')
                            ->count();

                        // Calculate vacant units
                        $vacant_units = $total_floor_units - $total_floor_occupants;

                        // Structure the data for this floor
                        $occupied_members = $grouped_members->has($floor_number)
                            ? $grouped_members[$floor_number]->map(function ($member) {
                                return [
                                    'name' => $member->name,
                                    'aprt_no' => $member->aprt_no,
                                    'floor_number' => $member->floor_number,
                                    'block_name' => $member->block_name,
                                ];
                            })->toArray()
                            : [
                                [
                                    'name' => null,
                                    'aprt_no' => null,
                                    'floor_number' => null,
                                    'block_name' => null,
                                ]
                            ];

                        $transformed_data[] = [
                            'floor_number' => (int) $floor_number,
                            'total_units' => (int) $total_floor_units,
                            'total_occupants' => (int) $total_floor_occupants,
                            'total_vacants' => $vacant_units,
                            'occupied_members' => $occupied_members,
                        ];
                    }
                }
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'occupied_members' => $occupied_members_paginated,  // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }

    public function memberListN(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_name' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
                'search' => 'nullable|string|max:255',  // New name filter validation
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units and floors for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
            $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

            // Get the input for filters
            $floor_number = $request->input('floor_number');
            $block_name = $request->input('block_name');
            $search = $request->input('search');  // New filter for member name

            // Start the query
            $query = Member::select('members.name', 'members.aprt_no', 'members.floor_number', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $society_id);

            // If block_name is provided and no floor_number, handle only block data
            if ($block_name && !$floor_number) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)
                    ->pluck('id')
                    ->toArray();

                // Get members for that block
                $query->whereIn('members.block_id', $blockIds);
            } else {
                // Apply the floor_number filter if it's provided
                if ($floor_number) {
                    $query->where('members.floor_number', $floor_number);
                }
                if ($block_name) {
                    $blockIds = Block::where('name', $block_name)
                        ->where('society_id', $society_id)
                        ->pluck('id')
                        ->toArray();

                    $query->whereIn('members.block_id', $blockIds);
                }
            }

            // Apply the search filter if it's provided
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('members.name', 'like', '%' . $search . '%')
                        ->orWhere('members.aprt_no', 'like', '%' . $search . '%');
                });
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('members.floor_number', 'asc')->paginate(10);
            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Handle block-only data if block_name is provided without floor_number
            if ($block_name && !$floor_number) {
                // Get the total units and occupants for the block
                $total_block_units = Block::where('id', $blockIds[0])->value('total_units');
                $total_block_occupants = Member::where('society_id', $society_id)
                    ->whereIn('block_id', $blockIds)
                    ->where('status', 'active')
                    ->count();

                // Calculate vacant units for the block
                $vacant_units = $total_block_units - $total_block_occupants;

                // Collect members of the block
                $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                    return $membersOnBlock->map(function ($member) {
                        return [
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                            'floor_number' => $member->floor_number,  // Include floor_number here
                            'block_name' => $member->block_name,
                        ];
                    });
                })->isEmpty() ? [
                    [
                        'name' => null,
                        'aprt_no' => null,
                        'floor_number' => null,
                        'block_name' => null,
                    ]
                ] : $grouped_members->toArray();

                // Use the first member's floor_number for the block response
                $first_member_floor_number = isset($occupied_members[0]) ? $occupied_members[0]['floor_number'] : null;

                // Structure the response for block data
                $transformed_data[] = [
                    'floor_number' => $first_member_floor_number,  // Set first member's floor_number
                    'total_units' => (int) $total_block_units,
                    'total_occupants' => (int) $total_block_occupants,
                    'total_vacants' => $vacant_units,
                    'occupied_members' => $occupied_members,
                ];

            } else {
                // If name filter is applied, set certain keys to null
                if ($search) {
                    $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                        return $membersOnBlock->map(function ($member) {
                            return [
                                'name' => $member->name,
                                'aprt_no' => $member->aprt_no,
                                'floor_number' => $member->floor_number,
                                'block_name' => $member->block_name,
                            ];
                        });
                    })->isEmpty() ? [
                        [
                            'name' => null,
                            'aprt_no' => null,
                            'floor_number' => null,
                            'block_name' => null,
                        ]
                    ] : $grouped_members->toArray();

                    $transformed_data[] = [
                        'floor_number' => null,  // Set to null since name filter is applied
                        'total_units' => null,    // Set to null since name filter is applied
                        'total_occupants' => null, // Set to null since name filter is applied
                        'total_vacants' => null,   // Set to null since name filter is applied
                        'occupied_members' => $occupied_members,
                    ];
                } else {
                    // Loop through the filtered floors or all floors if no filter is applied
                    $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

                    foreach ($floors_to_loop as $floor_number) {
                        // Query to get total units for the current floor
                        $total_floor_units = (int) $society->floor_units;

                        // Count the occupied members on this floor
                        $total_floor_occupants = Member::where('society_id', $society_id)
                            ->where('floor_number', $floor_number)
                            ->where('status', 'active')
                            ->count();

                        // Calculate vacant units
                        $vacant_units = $total_floor_units - $total_floor_occupants;

                        // Structure the data for this floor
                        $occupied_members = $grouped_members->has($floor_number)
                            ? $grouped_members[$floor_number]->map(function ($member) {
                                return [
                                    'name' => $member->name,
                                    'aprt_no' => $member->aprt_no,
                                    'floor_number' => $member->floor_number,
                                    'block_name' => $member->block_name,
                                ];
                            })->toArray()
                            : [
                                [
                                    'name' => null,
                                    'aprt_no' => null,
                                    'floor_number' => null,
                                    'block_name' => null,
                                ]
                            ];

                        $transformed_data[] = [
                            'floor_number' => (int) $floor_number,
                            'total_units' => (int) $total_floor_units,
                            'total_occupants' => (int) $total_floor_occupants,
                            'total_vacants' => $vacant_units,
                            'occupied_members' => $occupied_members,
                        ];
                    }
                }
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'occupied_members' => $occupied_members_paginated,  // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function memberListoldFinal(Request $request)
    {
        try {
            // Validate search input if provided
            $validator = validator($request->all(), [
                'block_name' => 'nullable|string|max:255',
                'floor_number' => 'nullable|string|max:255',
                'search' => 'nullable|string|max:255',  // New name filter validation
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Get total units and floors for this society
            $society = Society::where('status', 'active')->where('id', $society_id)->first();
            if (!$society) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $total_units = (int) $society->floors * (int) $society->floor_units;
            $occupied = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $societyTotalFloors = (int) $society->floors;  // Get total floors in the society
            $allFloors = range(1, $societyTotalFloors);  // Generate the list of floors (1, 2, ..., societyTotalFloors)

            // Get the input for filters
            $floor_number = $request->input('floor_number');
            $block_name = $request->input('block_name');
            $search = $request->input('search');  // New filter for member name

            // Start the query
            $query = Member::select('members.name', 'members.user_id', 'members.phone', 'members.aprt_no', 'members.floor_number', 'blocks.name as block_name', 'users.image as image')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->join('users', 'members.user_id', '=', 'users.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $society_id);

            // If block_name is provided and no floor_number, handle only block data
            if ($block_name && !$floor_number) {
                $blockIds = Block::where('name', $block_name)
                    ->where('society_id', $society_id)
                    ->pluck('id')
                    ->toArray();

                // Get members for that block
                $query->whereIn('members.block_id', $blockIds);
            } else {
                // Apply the floor_number filter if it's provided
                if ($floor_number) {
                    $query->where('members.floor_number', $floor_number);
                }
                if ($block_name) {
                    $blockIds = Block::where('name', $block_name)
                        ->where('society_id', $society_id)
                        ->pluck('id')
                        ->toArray();

                    $query->whereIn('members.block_id', $blockIds);
                }
            }

            // Apply the search filter if it's provided
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('members.name', 'like', '%' . $search . '%')
                        ->orWhere('members.aprt_no', 'like', '%' . $search . '%');
                });
            }

            // Paginate the results
            $occupied_members_paginated = $query->orderBy('members.floor_number', 'asc')->paginate(10);
            // Group the paginated members by floor_number
            $grouped_members = $occupied_members_paginated->getCollection()->groupBy('floor_number');

            // Initialize an empty array to store the transformed data
            $transformed_data = [];

            // Handle block-only data if block_name is provided without floor_number
            if ($block_name && !$floor_number) {
                // Get the total units and occupants for the block
                $total_block_units = Block::where('id', $blockIds[0])->value('total_units');
                $total_block_occupants = Member::where('society_id', $society_id)
                    ->whereIn('block_id', $blockIds)
                    ->where('status', 'active')
                    ->count();

                // Calculate vacant units for the block
                $vacant_units = $total_block_units - $total_block_occupants;

                // Collect members of the block
                $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                    return $membersOnBlock->map(function ($member) {
                        return [
                            'user_id' => $member->user_id,
                            'name' => $member->name,
                            'aprt_no' => $member->aprt_no,
                            'floor_number' => $member->floor_number,
                            'block_name' => $member->block_name,
                            'phone' => $member->phone,
                            'image' => !empty($member->image) ? url('storage/' . $member->image) : null,
                        ];
                    });
                })->toArray();

                // if (empty($occupied_members)) {
                //     $occupied_members = [
                //         [
                //             'name' => null,
                //             'aprt_no' => null,
                //             'floor_number' => null,
                //             'block_name' => null,
                //         ]
                //     ];
                // }

                $moreVacantUnit = 0;
                if (empty($occupied_members)) {

                    $moreVacantUnit = $total_block_occupants;
                } elseif (count($occupied_members) < $total_block_occupants) {
                    $moreVacantUnit = $total_block_occupants - count($occupied_members);
                }
                $vacantList = $vacant_units + $moreVacantUnit;

                // Create vacant members array
                $vacant_members = array_fill(0, $vacantList, [
                    'user_id' => null,
                    'name' => null,
                    'aprt_no' => null,
                    'floor_number' => null,
                    'block_name' => null,
                    'phone' => null,
                    'image' => null,
                ]);

                // Merge occupied and vacant members
                $members = array_merge($occupied_members, $vacant_members);

                // Structure the response for block data
                $transformed_data[] = [
                    'floor_number' => null,
                    'total_units' => (int) $total_block_units,
                    'total_occupants' => (int) $total_block_occupants,
                    'total_vacants' => $vacant_units,
                    'members' => $members,  // Updated key
                ];

            } else {
                // If name filter is applied, set certain keys to null
                if ($search) {
                    $occupied_members = $grouped_members->flatMap(function ($membersOnBlock) {
                        return $membersOnBlock->map(function ($member) {
                            return [
                                'user_id' => $member->user_id,
                                'name' => $member->name,
                                'aprt_no' => $member->aprt_no,
                                'floor_number' => $member->floor_number,
                                'block_name' => $member->block_name,
                                'phone' => $member->phone,
                                'image' => !empty($member->image) ? url('storage/' . $member->image) : null,
                            ];
                        });
                    })->toArray();

                    if (empty($occupied_members)) {
                        $occupied_members = [
                            [
                                'user_id' => null,
                                'name' => null,
                                'aprt_no' => null,
                                'floor_number' => null,
                                'block_name' => null,
                                'phone' => null,
                                'image' => null,
                            ]
                        ];
                    }

                    $transformed_data[] = [
                        'floor_number' => null,  // Set to null since name filter is applied
                        'total_units' => null,    // Set to null since name filter is applied
                        'total_occupants' => null, // Set to null since name filter is applied
                        'total_vacants' => null,   // Set to null since name filter is applied
                        'members' => $occupied_members,  // Updated key
                    ];
                } else {
                    // Loop through the filtered floors or all floors if no filter is applied
                    $floors_to_loop = $floor_number ? [$floor_number] : $allFloors;

                    foreach ($floors_to_loop as $floor_number) {
                        // Query to get total units for the current floor
                        $total_floor_units = (int) $society->floor_units;

                        // Count the occupied members on this floor
                        $total_floor_occupants = Member::where('society_id', $society_id)
                            ->where('floor_number', $floor_number)
                            ->where('status', 'active')
                            ->count();

                        // Calculate vacant units
                        $vacant_units = $total_floor_units - $total_floor_occupants;

                        // Structure the data for this floor
                        $occupied_members = $grouped_members->has($floor_number)
                            ? $grouped_members[$floor_number]->map(function ($member) {
                                return [
                                    'user_id' => $member->user_id,
                                    'name' => $member->name,
                                    'aprt_no' => $member->aprt_no,
                                    'floor_number' => $member->floor_number,
                                    'block_name' => $member->block_name,
                                    'phone' => $member->phone,
                                    'image' => !empty($member->image) ? url('storage/' . $member->image) : null,
                                ];
                            })->toArray()
                            : [];

                        $moreVacantUnit = 0;
                        if (empty($occupied_members)) {

                            $moreVacantUnit = $total_floor_occupants;
                        } elseif (count($occupied_members) < $total_floor_occupants) {
                            $moreVacantUnit = $total_floor_occupants - count($occupied_members);
                        }
                        $vacantList = $vacant_units + $moreVacantUnit;

                        // Create vacant members array
                        $vacant_members = array_fill(0, $vacantList, [
                            'user_id' => null,
                            'name' => null,
                            'aprt_no' => null,
                            'floor_number' => null,
                            'block_name' => null,
                            'phone' => null,
                            'image' => null,
                        ]);

                        // Merge occupied and vacant members
                        $members = array_merge($occupied_members, $vacant_members);

                        $transformed_data[] = [
                            'floor_number' => (int) $floor_number,
                            'total_units' => (int) $total_floor_units,
                            'total_occupants' => (int) $total_floor_occupants,
                            'total_vacants' => $vacant_units,
                            'members' => $members,  // Updated key
                        ];
                    }
                }
            }

            // Update the paginated collection with the transformed data
            $occupied_members_paginated->setCollection(collect($transformed_data));

            $data = [
                'total_units' => $total_units,
                'occupied' => $occupied,
                'members' => $occupied_members_paginated,  // Paginated data with transformed structure
            ];

            return res(
                status: true,
                message: "Members retrieved successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // public function getMembers(Request $request)
    // {
    //     $request->validate([
    //         'block_name' => 'required|string',
    //         'floor_number' => 'nullable|integer'
    //     ]);

    //     $blockName = $request->input('block_name');
    //     $floorNumber = $request->input('floor_number');

    //     // Fetch the active society
    //     $logger = auth()->user();
    //     $society_id = $logger->society_id;
    //     $society = Society::where('status', 'active')
    //         ->where('id', $society_id)
    //         ->first();
    //     if (!$society) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No active society found',
    //             'data' => null
    //         ], 404);
    //     }

    //     // Fetch total units in the given block (and floor if provided)
    //     $blockQuery = Block::where('society_id', $society->id)
    //         ->where('name', $blockName);

    //     if (!empty($floorNumber)) {
    //         $blockQuery->where('floor', $floorNumber);
    //     }

    //     $totalUnits = $blockQuery->count();

    //     // Fetch blocks sorted by floor in ascending order
    //     $blocks = $blockQuery->orderBy('floor', 'asc')->get();

    //     // Fetch occupied members in the given block (and floor if provided)
    //     $occupiedQuery = Member::where('society_id', $society->id) // Filter by society ID
    //         ->where('status', 'active') // Only include active members
    //         ->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
    //             $query->select('id') // Select the IDs of blocks that match the given conditions
    //                 ->from('blocks') // The blocks table
    //                 ->where('name', $blockName); // Filter blocks by the given block name
    //             if (!empty($floorNumber)) {
    //                 $query->where('floor', $floorNumber); // If floor number is provided, filter by it as well
    //             }
    //         });

    //     $occupiedUnits = $occupiedQuery->count();

    //     // Fetch vacant units
    //     $vacantUnits = $totalUnits - $occupiedUnits;

    //     $membersQuery = Member::where('society_id', $society->id)
    //         ->where('status', 'active') // Filter by active members
    //         ->whereIn('block_id', $blocks->pluck('id')); // Filter by block_ids in the selected blocks

    //     $members = $membersQuery->get();
    //     // Fetch members with pagination
    //     // $members = Member::where('society_id', $society->id)
    //     //     ->whereHas('block', function ($q) use ($blockName, $floorNumber) {
    //     //         $q->where('name', $blockName);
    //     //         if (!empty($floorNumber)) {
    //     //             $q->where('floor', $floorNumber);
    //     //         }
    //     //     })
    //     //     ->paginate(10);

    //     // Prepare response
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Members retrieved successfully',
    //         'data' => [
    //             'total_units' => $totalUnits,
    //             'occupied' => $occupiedUnits,
    //             'vacant' => $vacantUnits,
    //             'members' => $members
    //         ]
    //     ]);
    // }

    public function getMembers2(Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'block_name' => 'required|string',
            'floor_number' => 'nullable|integer'
        ]);

        // Get the block name and floor number from the request
        $blockName = $request->input('block_name');
        $floorNumber = $request->input('floor_number');

        // Fetch the active society
        $logger = auth()->user();  // Get the logged-in user
        $society_id = $logger->society_id;  // Get society ID from the user
        $society = Society::where('status', 'active')
            ->where('id', $society_id)
            ->first();  // Find the active society based on society_id

        // If no active society found, return an error response
        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'No active society found',
                'data' => null
            ], 404);
        }

        // Fetch the blocks based on block name and floor number
        $blockQuery = Block::where('society_id', $society->id)
            ->where('name', $blockName);

        // If floor number is provided, add that to the query filter
        if (!empty($floorNumber)) {
            $blockQuery->where('floor', $floorNumber);
        }

        // Count the total number of units in the selected blocks
        $totalUnits = $blockQuery->count();

        // Fetch all blocks sorted by floor in ascending order
        $blocks = $blockQuery->orderBy('floor', 'asc')->get();

        // Fetch the active members associated with the selected blocks
        $occupiedQuery = Member::where('society_id', $society->id)
            ->where('status', 'active')  // Only active members
            ->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                // Fetch block IDs that match the given block name and floor (if provided)
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
                if (!empty($floorNumber)) {
                    $query->where('floor', $floorNumber);
                }
            });

        // Count the number of occupied units (i.e., active members in these blocks)
        $occupiedUnits = $occupiedQuery->count();

        // Calculate vacant units (total units minus occupied units)
        $vacantUnits = $totalUnits - $occupiedUnits;

        // Fetch the members in the selected blocks (with member details)
        $membersQuery = Member::where('society_id', $society->id)
            ->where('status', 'active')  // Only active members
            ->whereIn('block_id', $blocks->pluck('id'));  // Filter by block IDs of selected blocks

        // Get the member data for the selected blocks
        $members = $membersQuery->get();

        // Prepare the response with all required data
        return response()->json([
            'status' => true,
            'message' => 'Members retrieved successfully',
            'data' => [
                'total_units' => $totalUnits,  // Total units in the selected blocks
                'occupied' => $occupiedUnits,  // Number of occupied units
                'vacant' => $vacantUnits,  // Number of vacant units
                'members' => $members  // List of active members in the selected blocks
            ]
        ]);
    }

    public function getMembers3(Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'block_name' => 'required|string',
            'floor_number' => 'nullable|integer'
        ]);

        // Get the block name and floor number from the request
        $blockName = $request->input('block_name');
        $floorNumber = $request->input('floor_number');

        // Fetch the active society
        $logger = auth()->user();  // Get the logged-in user
        $society_id = $logger->society_id;  // Get society ID from the user
        $society = Society::where('status', 'active')
            ->where('id', $society_id)
            ->first();  // Find the active society based on society_id

        // If no active society found, return an error response
        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'No active society found',
                'data' => null
            ], 404);
        }

        // Fetch the blocks based on block name and floor number
        $blockQuery = Block::where('society_id', $society->id)
            ->where('name', $blockName);

        // If floor number is provided, add that to the query filter
        if (!empty($floorNumber)) {
            $blockQuery->where('floor', $floorNumber);
        }

        // Fetch blocks sorted by floor
        $blocks = $blockQuery->orderBy('floor', 'asc')->get();

        // Prepare response structure for properties
        $properties = [];

        // Group blocks by floors
        foreach ($blocks as $block) {
            // Check if this floor already exists in properties array
            if (!isset($properties[$block->floor])) {
                $properties[$block->floor] = [
                    'floor' => $block->floor,
                    'property_numbers' => []
                ];
            }

            // Get the members in this block
            $members = Member::where('society_id', $society->id)
                ->where('status', 'active')
                ->where('block_id', $block->id)
                ->get();

            // Add block details and members (if any) to the property_numbers array
            $properties[$block->floor]['property_numbers'][] = [
                'block_id' => $block->id,
                'name' => $block->name,
                'floor' => $block->floor,
                'property_number' => $block->property_number,
                'block_details' => $block,  // All details of the block
                'members' => $members->isEmpty() ? null : $members
            ];
        }

        // Return the structured data
        return response()->json([
            'status' => true,
            'message' => 'Properties and members retrieved successfully',
            'data' => [
                'properties' => array_values($properties)  // Re-indexing the array to ensure it's properly indexed
            ]
        ]);
    }

    public function getMembers4(Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'block_name' => 'required|string',
            'floor_number' => 'nullable|integer'
        ]);

        // Get the block name and floor number from the request
        $blockName = $request->input('block_name');
        $floorNumber = $request->input('floor_number');

        // Fetch the active society
        $logger = auth()->user();  // Get the logged-in user
        $society_id = $logger->society_id;  // Get society ID from the user
        $society = Society::where('status', 'active')
            ->where('id', $society_id)
            ->first();  // Find the active society based on society_id

        // If no active society found, return an error response
        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'No active society found',
                'data' => null
            ], 404);
        }

        // Fetch the blocks based on block name and floor number
        $blockQuery = Block::where('society_id', $society->id)
            ->where('name', $blockName);

        // If floor number is provided, add that to the query filter
        if (!empty($floorNumber)) {
            $blockQuery->where('floor', $floorNumber);
        }

        // Fetch blocks sorted by floor
        $blocks = $blockQuery->orderBy('floor', 'asc')->get();

        // Prepare response structure for properties
        $properties = [];
        $totalUnits = 0;
        $occupiedUnits = 0;

        // Group blocks by floors
        foreach ($blocks as $block) {
            // Check if this floor already exists in properties array
            if (!isset($properties[$block->floor])) {
                $properties[$block->floor] = [
                    'floor' => $block->floor,
                    'property_numbers' => [],
                    'total_units' => 0,
                    'occupied' => 0,
                    'vacant' => 0
                ];
            }

            // Add 1 to the total units for this block
            $properties[$block->floor]['total_units']++;

            // Get the members in this block
            $members = Member::where('society_id', $society->id)
                ->where('status', 'active')
                ->where('block_id', $block->id)
                ->get();

            // If members exist, count them as occupied
            if ($members->isNotEmpty()) {
                $properties[$block->floor]['occupied']++;
            }

            // Add block details and members (if any) to the property_numbers array
            $properties[$block->floor]['property_numbers'][] = [
                'block_id' => $block->id,
                'name' => $block->name,
                'floor' => $block->floor,
                'property_number' => $block->property_number,
                'block_details' => $block,  // All details of the block
                'members' => $members->isEmpty() ? null : $members
            ];
        }

        // Calculate vacant units
        foreach ($properties as &$property) {
            $property['vacant'] = $property['total_units'] - $property['occupied'];
        }

        // Return the structured data with total_units, occupied, vacant
        return response()->json([
            'status' => true,
            'message' => 'Properties and members retrieved successfully',
            'data' => [
                'properties' => array_values($properties),  // Re-indexing the array to ensure it's properly indexed
            ]
        ]);
    }

    public function getMembers5(Request $request)
    {
        $request->validate([
            'block_name' => 'required|string',
            'floor_number' => 'nullable|integer'
        ]);

        $blockName = $request->input('block_name');
        $floorNumber = $request->input('floor_number');

        // Fetch the active society
        $logger = auth()->user();
        $society_id = $logger->society_id;
        $society = Society::where('status', 'active')
            ->where('id', $society_id)
            ->first();

        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'No active society found',
                'data' => null
            ], 404);
        }

        // Fetch blocks based on name and floor number (if provided)
        $blockQuery = Block::where('society_id', $society->id)
            ->where('name', $blockName);

        if (!empty($floorNumber)) {
            $blockQuery->where('floor', $floorNumber);
        }

        $blocks = $blockQuery->orderBy('floor', 'asc')->get();

        $properties = [];

        foreach ($blocks as $block) {
            // Initialize floor if not already in properties
            if (!isset($properties[$block->floor])) {
                $properties[$block->floor] = [
                    'floor' => $block->floor,
                    'property_numbers' => [],
                    'total_units' => 0,
                    'occupied' => 0,
                    'vacant' => 0
                ];
            }

            // Increment total units for this floor
            $properties[$block->floor]['total_units']++;

            // Fetch members for this block
            $members = Member::where('society_id', $society->id)
                ->where('status', 'active')
                ->where('block_id', $block->id)
                ->get();

            // If there are members, increment the occupied count
            if ($members->isNotEmpty()) {
                $properties[$block->floor]['occupied']++;
            }

            // Add block and member details to the property
            $properties[$block->floor]['property_numbers'][] = [
                'block_id' => $block->id,
                'name' => $block->name,
                'floor' => $block->floor,
                'property_number' => $block->property_number,
                'block_details' => $block,  // Block details (all columns)
                'members' => $members->isEmpty() ? null : $members  // Members if any, otherwise null
            ];
        }

        // Calculate vacant units (total_units - occupied)
        foreach ($properties as &$property) {
            $property['vacant'] = $property['total_units'] - $property['occupied'];
        }

        // Return the structured data
        return response()->json([
            'status' => true,
            'message' => 'Properties and members retrieved successfully',
            'data' => [
                'properties' => array_values($properties)  // Re-index the properties to ensure proper indexing
            ]
        ]);
    }

    public function getMembers6(Request $request)
    {
        $request->validate([
            'block_name' => 'required|string',
            'floor_number' => 'nullable|integer'
        ]);

        $blockName = $request->input('block_name');
        $floorNumber = $request->input('floor_number');

        // Fetch the active society
        $logger = auth()->user();
        $society_id = $logger->society_id;

        // *************************
        $society = Society::where('status', 'active')
            ->where('id', $society_id)
            ->first();  // Find the active society based on society_id

        // If no active society found, return an error response
        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'No active society found',
                'data' => null
            ], 404);
        }

        // Fetch the blocks based on block name and floor number
        $blockQuery = Block::where('society_id', $society->id)
            ->where('name', $blockName);

        // If floor number is provided, add that to the query filter
        if (!empty($floorNumber)) {
            $blockQuery->where('floor', $floorNumber);
        }

        // Count the total number of units in the selected blocks
        $totalUnits_Root = $blockQuery->count();

        // Fetch all blocks sorted by floor in ascending order
        $blocks = $blockQuery->orderBy('floor', 'asc')->get();

        // Fetch the active members associated with the selected blocks
        $occupiedQuery = Member::where('society_id', $society->id)
            ->where('status', 'active')  // Only active members
            ->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                // Fetch block IDs that match the given block name and floor (if provided)
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
                if (!empty($floorNumber)) {
                    $query->where('floor', $floorNumber);
                }
            });

        // Count the number of occupied units (i.e., active members in these blocks)
        $occupiedUnits_Root = $occupiedQuery->count();

        // Calculate vacant units (total units minus occupied units)
        $vacantUnits_Root = $totalUnits_Root - $occupiedUnits_Root;
        // *************************
        $society = Society::where('status', 'active')
            ->where('id', $society_id)
            ->first();

        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'No active society found',
                'data' => null
            ], 404);
        }

        // Fetch blocks based on name and floor number (if provided)
        $blockQuery = Block::with('member_info')->where('society_id', $society->id)
            ->where('name', $blockName);

        if (!empty($floorNumber)) {
            $blockQuery->where('floor', $floorNumber);
        }

        $blocks = $blockQuery->orderBy('floor', 'asc')->get();

        $properties = [];

        $totalUnits = 0; // Total units across all floors
        $occupied = 0; // Total occupied units across all floors
        $vacant = 0; // Total vacant units across all floors

        foreach ($blocks as $block) {
            // Initialize floor if not already in properties
            if (!isset($properties[$block->floor])) {
                $properties[$block->floor] = [
                    'floor' => $block->floor,
                    'property_numbers' => [],
                    'total_units' => 0,
                    'occupied' => 0,
                    'vacant' => 0
                ];
            }

            // Increment total units for this floor
            $properties[$block->floor]['total_units']++;

            // Increment global total units
            $totalUnits++;

            // Fetch members for this block
            $members = Member::where('society_id', $society->id)
                ->where('status', 'active')
                ->where('block_id', $block->id)
                ->get();

            // If there are members, increment the occupied count
            if ($members->isNotEmpty()) {
                $properties[$block->floor]['occupied']++;
                $occupied++;
            }

            // Add block and member details to the property
            // $properties[$block->floor]['property_numbers'][] = [
            //     // 'block_id' => $block->id,
            //     // 'name' => $block->name,
            //     // 'floor' => $block->floor,
            //     // 'property_number' => $block->property_number,
            //     // 'block_details' => $block,  // Block details (all columns)
            //     // 'members' => $members->isEmpty() ? null : $members  // Members if any, otherwise null
            //     $block,  // Block details (all columns)
            //     $block->member => ($members->isEmpty() ? null : $members)
            // ];
            // return $block;
            $properties[$block->floor]['property_numbers'][] = $block;
            //[
            // $block,  // Block details (all columns)
            // 'member_info' => $members->isEmpty() ? null : $members  // Add the member info under 'member_info' key
            // ];

        }

        // Calculate vacant units for each floor (total_units - occupied)
        foreach ($properties as &$property) {
            $property['vacant'] = $property['total_units'] - $property['occupied'];
            $vacant += $property['vacant'];
        }

        // Return the structured data with totals included
        return response()->json([
            'status' => true,
            'message' => 'Properties and members retrieved successfully',
            'data' => [
                'total_units' => $totalUnits_Root, // Total units across all floors
                'occupied' => $occupiedUnits_Root, // Total occupied units across all floors
                'vacant' => $vacantUnits_Root, // Total vacant units across all floors
                'properties' => array_values($properties)  // Re-index the properties to ensure proper indexing
            ]
        ]);
    }

    public function getMembers(Request $request)
    {
        // Validate the request inputs
        $validator = validator($request->all(), [
            'block_name' => 'required|string',
            'floor_number' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Fetch the active society
            $society = Society::where('status', 'active')
                ->where('id', $society_id)
                ->first();

            // If no active society found, return an error response
            if (!$society) {
                return res(
                    status: false,
                    message: 'No active society found',
                    data: null,
                    code: HTTP_OK
                );
            }

            // Get the input values from the request
            $blockName = $request->input('block_name');
            $floorNumber = $request->input('floor_number');

            // Fetch the blocks based on block name and floor number
            $blockQuery = Block::where('society_id', $society->id)
                ->where('name', $blockName);

            // If floor number is provided, add it to the query filter
            if (!empty($floorNumber)) {
                $blockQuery->where('floor', $floorNumber);
            }

            // Count the total number of units in the selected blocks
            $totalUnits_Root = $blockQuery->count();

            // Fetch all blocks sorted by floor in ascending order
            $blocks = $blockQuery->orderBy('floor', 'asc')->get();

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Member::where('society_id', $society->id)
                ->where('status', 'active') // Only active members
                ->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                    // Fetch block IDs that match the given block name and floor (if provided)
                    $query->select('id')
                        ->from('blocks')
                        ->where('name', $blockName);
                    if (!empty($floorNumber)) {
                        $query->where('floor', $floorNumber);
                    }
                });

            // Count the number of occupied units (i.e., active members in these blocks)
            $occupiedUnits_Root = $occupiedQuery->count();

            // Calculate vacant units (total units minus occupied units)
            $vacantUnits_Root = $totalUnits_Root - $occupiedUnits_Root;

            $properties = [];
            $totalUnits = 0;
            $occupied = 0;
            $vacant = 0;

            // Loop through each block and build the properties array
            foreach ($blocks as $block) {
                // Initialize the floor if not already in properties
                if (!isset($properties[$block->floor])) {
                    $properties[$block->floor] = [
                        'floor' => $block->floor,
                        'property_numbers' => [],
                        'total_units' => 0,
                        'occupied' => 0,
                        'vacant' => 0
                    ];
                }

                // Increment total units for this floor
                $properties[$block->floor]['total_units']++;

                // Increment global total units
                $totalUnits++;

                // Fetch members for this block
                $members = Member::where('society_id', $society->id)
                    ->where('status', 'active')
                    ->where('block_id', $block->id)
                    ->get();

                // If there are members, increment the occupied count
                if ($members->isNotEmpty()) {
                    $properties[$block->floor]['occupied']++;
                    $occupied++;
                }

                // Add block and member details to the property
                $properties[$block->floor]['property_numbers'][] = [
                    $block,  // Block details (all columns)
                    'member_info' => $members->isEmpty() ? null : $members  // Add member info under 'member_info'
                ];
            }

            // Calculate vacant units for each floor (total_units - occupied)
            foreach ($properties as &$property) {
                $property['vacant'] = $property['total_units'] - $property['occupied'];
                $vacant += $property['vacant'];
            }

            // Return the structured data with totals included
            return res(
                status: true,
                message: 'Properties and members retrieved successfully',
                data: [
                    'total_units' => $totalUnits_Root, // Total units across all floors
                    'occupied' => $occupiedUnits_Root, // Total occupied units across all floors
                    'vacant' => $vacantUnits_Root, // Total vacant units across all floors
                    'properties' => array_values($properties)  // Re-index the properties to ensure proper indexing
                ],
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

    public function memberListLive(Request $request)
    {
        // Validate the request inputs
        $validator = validator($request->all(), [
            'block_name' => 'required|string',
            'floor_number' => 'nullable',
            'search' => 'nullable|string|max:255', // Added search parameter
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Fetch the active society
            $society = Society::where('status', 'active')
                ->where('id', $society_id)
                ->first();

            // If no active society found, return an error response
            if (!$society) {
                return res(
                    status: false,
                    message: 'No active society found',
                    data: null,
                    code: HTTP_OK
                );
            }

            // Get the input values from the request
            $blockName = $request->input('block_name');
            $floorNumber = $request->input('floor_number');
            $searchText = $request->input('search'); // Get search input

            // Fetch the blocks based on block name and floor number
            $blockQuery = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);

            // If floor number is provided, add it to the query filter
            if (!empty($floorNumber)) {
                $blockQuery->where('floor', $floorNumber);
            }

            // Count the total number of units in the selected blocks
            $blockQueryForCount = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);
            $totalUnits_Root = $blockQueryForCount->count();

            // Fetch all blocks sorted by floor in ascending order
            $blocks = $blockQuery->orderBy('floor', 'asc')->get();

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Member::where('society_id', $society->id)
                ->where('status', 'active'); // Only active members

            // Apply search filter for member.name or member.phone if search is provided
            if (!empty($searchText)) {
                $occupiedQuery->where(function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%');
                    // ->orWhere('phone', 'like', '%' . $searchText . '%');
                });
            }

            $occupiedQuery->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                // Fetch block IDs that match the given block name and floor (if provided)
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
                if (!empty($floorNumber)) {
                    $query->where('floor', $floorNumber);
                }
            });


            $occupiedQueryForCount = Member::where('society_id', $society->id)
                ->where('status', 'active');
            // Count the number of occupied units (i.e., active members in these blocks)
            $occupiedQueryForCount->whereIn('block_id', function ($query) use ($blockName) {
                // Fetch block IDs that match the given block name and floor (if provided)
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
            });
            $occupiedUnits_Root = $occupiedQueryForCount->count();

            // Calculate vacant units (total units minus occupied units)
            $vacantUnits_Root = $totalUnits_Root - $occupiedUnits_Root;

            $properties = [];
            $totalUnits = 0;
            $occupied = 0;
            $vacant = 0;

            // Loop through each block and build the properties array
            foreach ($blocks as $block) {
                // Initialize the floor if not already in properties
                if (!isset($properties[$block->floor])) {
                    $properties[$block->floor] = [
                        'floor' => $block->floor,
                        'property_numbers' => [],
                        'total_units' => 0,
                        'occupied' => 0,
                        'vacant' => 0
                    ];
                }

                // Increment total units for this floor
                $properties[$block->floor]['total_units']++;

                // Increment global total units
                $totalUnits++;

                // Fetch members for this block, applying the search filter if available
                $members = $occupiedQuery->where('block_id', $block->id)->get();

                // If there are members, increment the occupied count
                if ($members->isNotEmpty()) {
                    $properties[$block->floor]['occupied']++;
                    $occupied++;
                }

                // Add block and member details to the property
                $properties[$block->floor]['property_numbers'][] = $block;
            }

            // Calculate vacant units for each floor (total_units - occupied)
            foreach ($properties as &$property) {
                $property['vacant'] = $property['total_units'] - $property['occupied'];
                $vacant += $property['vacant'];
            }

            // Return the structured data with totals included
            return res(
                status: true,
                message: 'Properties and members retrieved successfully',
                data: [
                    'total_units' => $totalUnits_Root, // Total units across all floors
                    'occupied' => $occupiedUnits_Root, // Total occupied units across all floors
                    'vacant' => $vacantUnits_Root, // Total vacant units across all floors
                    'properties' => array_values($properties)  // Re-index the properties to ensure proper indexing
                ],
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

    public function memberListSeButFalt(Request $request)
    {
        // Validate the request inputs
        $validator = validator($request->all(), [
            'block_name' => 'required|string',
            'floor_number' => 'nullable',
            'search' => 'nullable|string|max:255', // Added search parameter
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Fetch the active society
            $society = Society::where('status', 'active')
                ->where('id', $society_id)
                ->first();

            // If no active society found, return an error response
            if (!$society) {
                return res(
                    status: false,
                    message: 'No active society found',
                    data: null,
                    code: HTTP_OK
                );
            }

            // Get the input values from the request
            $blockName = $request->input('block_name');
            $floorNumber = $request->input('floor_number');
            $searchText = $request->input('search'); // Get search input

            // Fetch the blocks based on block name and floor number
            $blockQuery = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);

            // If floor number is provided, add it to the query filter
            if (!empty($floorNumber)) {
                $blockQuery->where('floor', $floorNumber);
            }

            // Fetch all blocks sorted by floor in ascending order
            $blocks = $blockQuery->orderBy('floor', 'asc')->get();

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Member::where('society_id', $society->id)
                ->where('status', 'active'); // Only active members

            // Apply search filter for member.name if search is provided
            if (!empty($searchText)) {
                $occupiedQuery->where(function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%');
                });
            }

            $occupiedQuery->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                // Fetch block IDs that match the given block name and floor (if provided)
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
                if (!empty($floorNumber)) {
                    $query->where('floor', $floorNumber);
                }
            });

            // Fetch members based on the updated query
            $members = $occupiedQuery->get();

            // Initialize variables
            $properties = [];
            $totalUnits = 0;
            $occupied = 0;
            $vacant = 0;

            // Loop through the blocks and filter out those with no matching members
            foreach ($blocks as $block) {
                // Fetch members for this block
                $blockMembers = $members->filter(function ($member) use ($block) {
                    return $member->block_id === $block->id;
                });

                // If no matching members for this block, skip it
                if ($blockMembers->isEmpty()) {
                    continue;
                }

                // Initialize the floor if not already in properties
                if (!isset($properties[$block->floor])) {
                    $properties[$block->floor] = [
                        'floor' => $block->floor,
                        'property_numbers' => [],
                        'total_units' => 0,
                        'occupied' => 0,
                        'vacant' => 0
                    ];
                }

                // Increment total units for this floor
                $properties[$block->floor]['total_units']++;

                // Increment global total units
                $totalUnits++;

                // Add block and member details to the property
                $properties[$block->floor]['property_numbers'][] = $block;

                // Increment occupied units count
                $properties[$block->floor]['occupied']++;
                $occupied++;
            }

            // Calculate vacant units for each floor (total_units - occupied)
            foreach ($properties as &$property) {
                $property['vacant'] = $property['total_units'] - $property['occupied'];
                $vacant += $property['vacant'];
            }

            // Return the structured data with totals included
            return res(
                status: true,
                message: 'Properties and members retrieved successfully',
                data: [
                    'total_units' => $totalUnits, // Total units across all floors
                    'occupied' => $occupied, // Total occupied units across all floors
                    'vacant' => $vacant, // Total vacant units across all floors
                    'properties' => array_values($properties)  // Re-index the properties to ensure proper indexing
                ],
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

    public function memberListErr(Request $request)
    {
        // Validate the request inputs
        $validator = validator($request->all(), [
            'block_name' => 'required|string',
            'floor_number' => 'nullable',
            'search' => 'nullable|string|max:255', // Added search parameter
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            // Fetch the active society
            $society = Society::where('status', 'active')
                ->where('id', $society_id)
                ->first();

            // If no active society found, return an error response
            if (!$society) {
                return res(
                    status: false,
                    message: 'No active society found',
                    data: null,
                    code: HTTP_OK
                );
            }

            // Get the input values from the request
            $blockName = $request->input('block_name');
            $floorNumber = $request->input('floor_number');
            $searchText = $request->input('search'); // Get search input

            // Fetch the blocks based on block name and floor number
            $blockQuery = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);

            // If floor number is provided, add it to the query filter
            if (!empty($floorNumber)) {
                $blockQuery->where('floor', $floorNumber);
            }

            // Count the total number of units in the selected blocks
            $blockQueryForCount = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);
            $totalUnits_Root = $blockQueryForCount->count();

            // Fetch all blocks sorted by floor in ascending order
            $blocks = $blockQuery->orderBy('floor', 'asc')->get();

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Member::where('society_id', $society->id)
                ->where('status', 'active'); // Only active members

            // Apply search filter for member.name or member.phone if search is provided
            if (!empty($searchText)) {
                $occupiedQuery->where(function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%');
                    // ->orWhere('phone', 'like', '%' . $searchText . '%');
                });
            }

            $occupiedQuery->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                // Fetch block IDs that match the given block name and floor (if provided)
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
                if (!empty($floorNumber)) {
                    $query->where('floor', $floorNumber);
                }
            });

            // Count the number of occupied units (i.e., active members in these blocks)
            $occupiedQueryForCount = Member::where('society_id', $society->id)
                ->where('status', 'active');
            $occupiedQueryForCount->whereIn('block_id', function ($query) use ($blockName) {
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
            });
            $occupiedUnits_Root = $occupiedQueryForCount->count();

            // Calculate vacant units (total units minus occupied units)
            $vacantUnits_Root = $totalUnits_Root - $occupiedUnits_Root;

            $properties = [];
            $totalUnits = 0;
            $occupied = 0;
            $vacant = 0;

            // Loop through each block and build the properties array
            foreach ($blocks as $block) {
                // Initialize the floor if not already in properties
                if (!isset($properties[$block->floor])) {
                    $properties[$block->floor] = [
                        'floor' => $block->floor,
                        'property_numbers' => [],
                        'total_units' => 0,
                        'occupied' => 0,
                        'vacant' => 0
                    ];
                }

                // Increment total units for this floor
                $properties[$block->floor]['total_units']++;

                // Increment global total units
                $totalUnits++;

                // Fetch members for this block, applying the search filter if available
                $members = $occupiedQuery->where('block_id', $block->id)->get();

                // If there are members, increment the occupied count
                if ($members->isNotEmpty()) {
                    $properties[$block->floor]['occupied']++;
                    $occupied++;
                }

                // Add block and member details to the property
                $properties[$block->floor]['property_numbers'][] = $block;
            }

            // Calculate vacant units for each floor (total_units - occupied)
            foreach ($properties as &$property) {
                $property['vacant'] = $property['total_units'] - $property['occupied'];
                $vacant += $property['vacant'];
            }

            // When search is provided, only return floors with matching blocks
            if (!empty($searchText)) {
                $properties = array_filter($properties, function ($property) use ($searchText) {
                    // Only include floors with matching members
                    return collect($property['property_numbers'])->contains(function ($block) use ($searchText) {
                        return collect($block->member_info)->contains(function ($member) use ($searchText) {
                            return stripos($member->name, $searchText) !== false;
                        });
                    });
                });
            }

            // Return the structured data with totals included
            return res(
                status: true,
                message: 'Properties and members retrieved successfully',
                data: [
                    'total_units' => $totalUnits_Root, // Total units across all floors
                    'occupied' => $occupiedUnits_Root, // Total occupied units across all floors
                    'vacant' => $vacantUnits_Root, // Total vacant units across all floors
                    'properties' => array_values($properties)  // Re-index the properties to ensure proper indexing
                ],
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

    public function memberList(Request $request)
    {
        // Validate the request inputs
        $validator = validator($request->all(), [
            'block_name' => [
                'sometimes',
                'required_if:type,resident',
                'nullable',
                'string'
            ],
            'type' => 'required|in:resident,admin,guard,daily_help',
            'floor_number' => 'nullable',
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // If search is provided, use the separate function to filter results
        if ($request->type == 'resident' && $request->filled('search') && !empty($request->input('search'))) {
            return $this->filterMemberListBySearch($request);
        }

        // Existing code remains unchanged
        try {
            $logger = auth()->user();
            $society_id = $logger->society_id;

            $society = Society::where('status', 'active')
                ->where('id', $society_id)
                ->first();

            if (!$society) {
                return res(
                    status: false,
                    message: 'No active society found',
                    data: null,
                    code: HTTP_OK
                );
            }

            $blockName = $request->input('block_name');
            $floorNumber = $request->input('floor_number');

            $blockQuery = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);

            if (!empty($floorNumber)) {
                $blockQuery->where('floor', $floorNumber);
            }

            $blockQueryForCount = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);
            $totalUnits_Root = $blockQueryForCount->count();


            $occupiedQuery = Member::where('society_id', $society->id)
                ->where('status', 'active');

            $occupiedQuery->whereIn('block_id', function ($query) use ($blockName, $floorNumber) {
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
                if (!empty($floorNumber)) {
                    $query->where('floor', $floorNumber);
                }
            });

            $occupiedQueryForCount = Member::where('society_id', $society->id)
                ->where('status', 'active');

            $occupiedQueryForCount->whereIn('block_id', function ($query) use ($blockName) {
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
            });
            $occupiedUnits_Root = $occupiedQueryForCount->count();
            $vacantUnits_Root = $totalUnits_Root - $occupiedUnits_Root;

            $properties = [];
            $admin = null;
            $guards = null;
            $daily_help = null;
            // %%%%%%%%%%%%%%%%%%%%%%%%%%%%==:: resident type ::==%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
            if ($request->type == 'resident') {

                $blocks = $blockQuery->orderBy('floor', 'asc')->get();
                $totalUnits = 0;
                $occupied = 0;
                $vacant = 0;

                foreach ($blocks as $block) {
                    if (!isset($properties[$block->floor])) {
                        $properties[$block->floor] = [
                            'floor' => $block->floor,
                            'property_numbers' => [],
                            'total_units' => 0,
                            'occupied' => 0,
                            'vacant' => 0
                        ];
                    }

                    $properties[$block->floor]['total_units']++;
                    $totalUnits++;

                    $members = $occupiedQuery->where('block_id', $block->id)->get();

                    if ($members->isNotEmpty()) {
                        $properties[$block->floor]['occupied']++;
                        $occupied++;
                    }

                    $properties[$block->floor]['property_numbers'][] = $block;
                }

                foreach ($properties as &$property) {
                    $property['vacant'] = $property['total_units'] - $property['occupied'];
                    $vacant += $property['vacant'];
                }

            } elseif ($request->type == 'admin') {
                // %%%%%%%%%%%%%%%%%%%%%%%%%%%%==:: admin type ::==%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                $admin = User::where('status', 'active')
                    ->where('role', 'admin')
                    ->whereHas('member', function ($query) use ($society_id) {
                        $query->where('society_id', $society_id);
                    })
                    ->when($request->search, function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->get();

            } elseif ($request->type == 'guard') {
                // %%%%%%%%%%%%%%%%%%%%%%%%%%%%==:: guard type ::==%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                $guards = User::where('status', 'active')
                    ->where('role', 'staff_security_guard')
                    ->whereHas('staff', function ($query) use ($society_id) {
                        $query->where('society_id', $society_id);
                    })
                    ->when($request->search, function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->get();

            } elseif ($request->type == 'daily_help') {
                // %%%%%%%%%%%%%%%%%%%%%%%%%%%%==:: daily_help type ::==%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

                // if (auth()->user()->role === 'resident' || auth()->user()->role === 'admin') {

                //     $daily_help = User::where('status', 'active')
                //         ->whereHas('assignedDailyHelpStaffs', function ($query) {
                //             $query->where('member_user_id', auth()->id());
                //         })
                //         ->whereHas('staff', function ($query) use ($society_id) {
                //             $query->where('society_id', $society_id);
                //             $query->where('daily_help', 1);
                //         })
                //         ->when($request->search, function ($query) use ($request) {
                //             $query->where('name', 'like', '%' . $request->search . '%');
                //         })
                //         ->get();

                // } else {

                //     $daily_help = User::where('status', 'active')
                //         // ->where('role', 'staff_security_guard')
                //         ->whereHas('staff', function ($query) use ($society_id) {
                //             $query->where('society_id', $society_id);
                //             $query->where('daily_help', 1);
                //         })
                //         ->when($request->search, function ($query) use ($request) {
                //             $query->where('name', 'like', '%' . $request->search . '%');
                //         })->with([
                //                 'assignedDailyHelpMembers' => function ($query) {
                //                     $query->select(
                //                         'member_daily_help_staffs.staff_user_id',
                //                         'member_daily_help_staffs.member_user_id',
                //                         'member_daily_help_staffs.shift_from',
                //                         'member_daily_help_staffs.shift_to'
                //                     )->with([
                //                                 'memberUser.member' => function ($query) {
                //                                     $query->select(
                //                                         'members.id',
                //                                         'members.user_id',
                //                                         'members.name',
                //                                         'members.aprt_no',
                //                                         'members.floor_number',
                //                                         'members.unit_type',
                //                                         'members.phone',
                //                                         'blocks.name as block_name'
                //                                     )->join('blocks', 'members.block_id', '=', 'blocks.id');
                //                                 }
                //                             ]);
                //                 }
                //             ])
                //         ->get();
                // }

                if (auth()->user()->role === 'resident' || auth()->user()->role === 'admin') {

                    $daily_help = User::where('status', 'active')
                        ->whereHas('assignedDailyHelpMembers', function ($query) {
                            $query->where('member_user_id', auth()->id());
                        })
                        ->when($request->search, function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->with([
                            'assignedDailyHelpMembers' => function ($query) {
                                $query->select(
                                    'member_daily_help_staffs.staff_user_id',
                                    'member_daily_help_staffs.member_user_id',
                                    'member_daily_help_staffs.shift_from',
                                    'member_daily_help_staffs.shift_to'
                                )->with([
                                            'memberUser' => function ($query) {
                                                $query->select('id', 'role', 'name', 'email', 'phone', 'status'); // Select only required columns
                                                // ->withOutAppends(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']); // Hides appended attributes
                                            },
                                            'memberUser.member' => function ($query) {
                                                $query->select(
                                                    'members.id',
                                                    'members.user_id',
                                                    'members.name',
                                                    'members.aprt_no',
                                                    'members.floor_number',
                                                    'members.unit_type',
                                                    'members.phone',
                                                    'blocks.name as block_name'
                                                )->join('blocks', 'members.block_id', '=', 'blocks.id');
                                            }
                                        ]);
                            }
                        ])
                        ->get();

                    // Now hide the attributes AFTER fetching the data
                    $daily_help->each(function ($user) {
                        $user->assignedDailyHelpMembers->each(function ($staff) {
                            if ($staff->memberUser) {
                                $staff->memberUser->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
                            }
                        });
                    });

                } else {

                    // ====================guard side
                    $daily_help = User::where('status', 'active')
                        ->whereHas('staff', function ($query) use ($society_id) {
                            $query->where('society_id', $society_id);
                            $query->where('daily_help', 1);
                        })
                        ->when($request->search, function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->with([
                            'assignedDailyHelpMembers' => function ($query) {
                                $query->select(
                                    'member_daily_help_staffs.staff_user_id',
                                    'member_daily_help_staffs.member_user_id',
                                    'member_daily_help_staffs.shift_from',
                                    'member_daily_help_staffs.shift_to'
                                )->with([
                                            'memberUser' => function ($query) {
                                                $query->select('id', 'role', 'name', 'email', 'phone', 'status'); // Select only required columns
                                                // ->withOutAppends(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']); // Hides appended attributes
                                            },
                                            'memberUser.member' => function ($query) {
                                                $query->select(
                                                    'members.id',
                                                    'members.user_id',
                                                    'members.name',
                                                    'members.aprt_no',
                                                    'members.floor_number',
                                                    'members.unit_type',
                                                    'members.phone',
                                                    'blocks.name as block_name'
                                                )->join('blocks', 'members.block_id', '=', 'blocks.id');
                                            }
                                        ]);
                            }
                        ])
                        ->get();

                    // Now hide the attributes AFTER fetching the data
                    $daily_help->each(function ($user) {
                        $user->assignedDailyHelpMembers->each(function ($staff) {
                            if ($staff->memberUser) {
                                $staff->memberUser->makeHidden(['member_id', 'society_id', 'staff_id', 'last_checkin_detail']);
                            }
                        });
                    });

                }

            }
            return res(
                status: true,
                message: 'Properties and members retrieved successfully',
                data: [
                    'total_units' => $totalUnits_Root,
                    'occupied' => $occupiedUnits_Root,
                    'vacant' => $vacantUnits_Root,
                    'properties' => array_values($properties),
                    'admin' => $admin,
                    'guards' => $guards,
                    'daily_help' => $daily_help
                ],
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Function to handle filtering when search parameter is passed
     */
    private function filterMemberListBySearch(Request $request)
    {
        try {
            $logger = auth()->user();
            $society_id = $logger->society_id;
            $blockName = $request->input('block_name');
            $floorNumber = $request->input('floor_number');
            $searchText = $request->input('search');

            $society = Society::where('status', 'active')
                ->where('id', $society_id)
                ->first();

            if (!$society) {
                return res(
                    status: false,
                    message: 'No active society found',
                    data: null,
                    code: HTTP_OK
                );
            }
            // =================================================

            $blockQueryForCount = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName);
            $totalUnits_Root = $blockQueryForCount->count();

            $occupiedQuery = Member::where('society_id', $society->id)
                ->where('status', 'active');

            $occupiedQueryForCount = Member::where('society_id', $society->id)
                ->where('status', 'active');

            $occupiedQueryForCount->whereIn('block_id', function ($query) use ($blockName) {
                $query->select('id')
                    ->from('blocks')
                    ->where('name', $blockName);
            });
            $occupiedUnits_Root = $occupiedQueryForCount->count();
            $vacantUnits_Root = $totalUnits_Root - $occupiedUnits_Root;
            // =================================================
            // Fetch the block IDs where the search match is found
            $matchingBlockIds = Member::where('society_id', $society->id)
                ->where('status', 'active')
                ->where('name', 'like', '%' . $searchText . '%')
                ->pluck('block_id')
                ->toArray();

            if (empty($matchingBlockIds)) {
                return res(
                    status: true,
                    message: 'No matching members found',
                    data: [
                        'total_units' => 0,
                        'occupied' => 0,
                        'vacant' => 0,
                        'properties' => []
                    ],
                    code: HTTP_OK
                );
            }

            // Fetch blocks where members match the search
            $blocks = Block::with('member_info')->where('society_id', $society->id)
                ->where('name', $blockName)
                ->whereIn('id', $matchingBlockIds)
                ->orderBy('floor', 'asc')
                ->get();

            $properties = [];
            $totalUnits = $blocks->count();
            $occupied = count($matchingBlockIds);
            $vacant = $totalUnits - $occupied;

            foreach ($blocks as $block) {
                if (!isset($properties[$block->floor])) {
                    $properties[$block->floor] = [
                        'floor' => $block->floor,
                        'property_numbers' => [],
                        'total_units' => 0,
                        'occupied' => 0,
                        'vacant' => 0
                    ];
                }

                $properties[$block->floor]['total_units']++;
                $properties[$block->floor]['occupied']++;
                $properties[$block->floor]['property_numbers'][] = $block;
            }

            foreach ($properties as &$property) {
                $property['vacant'] = $property['total_units'] - $property['occupied'];
            }

            return res(
                status: true,
                message: 'Filtered properties based on search',
                data: [
                    'total_units' => $totalUnits_Root,
                    'occupied' => $occupiedUnits_Root,
                    'vacant' => $vacantUnits_Root,
                    'properties' => array_values($properties)
                ],
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function societyAllMembersold(Request $request)
    {
        // Validate request
        $validator = validator($request->all(), [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            $searchText = $request->input('search');

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Member::with(['block', 'user:id,image'])
                ->where('society_id', $society_id)
                ->where('status', 'active'); // Only active members

            // Apply search filter for member.name or member.phone if search is provided
            if (!empty($searchText)) {
                $occupiedQuery->where(function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%')
                        ->orWhere('phone', 'like', '%' . $searchText . '%'); // Uncomment if needed
                });
            }

            // Paginate with a fixed limit of 10 items per page
            $members = $occupiedQuery->paginate(10);

            return res(
                status: true,
                message: 'Members retrieved successfully',
                data: ['members' => $members],
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function societyAllMembers(Request $request)
    {
        // Validate request
        $validator = validator($request->all(), [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            $searchText = $request->input('search');

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Member::with([
                'block',
                'user:id,image' // Load only `image` from `users` table
            ])->where('society_id', $society_id)
                ->where('status', 'active'); // Only active members

            // Apply search filter for member.name or member.phone if search is provided
            if (!empty($searchText)) {
                $occupiedQuery->where(function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%')
                        ->orWhere('phone', 'like', '%' . $searchText . '%'); // Uncomment if needed
                });
            }

            // Paginate with a fixed limit of 10 items per page
            $occupiedQuery = $occupiedQuery->orderBy('name', 'asc');
            $members = $occupiedQuery->paginate(10);

            // Transform the members collection to extract `image` from `user`
            $members->getCollection()->transform(function ($member) {
                $member->image = url('storage/' . $member->user->image) ?? null;// Add `image` key
                unset($member->user); // Remove `user` relationship from response
                return $member;
            });

            return res(
                status: true,
                message: 'Members retrieved successfully',
                data: ['members' => $members],
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function societyProperties(Request $request)
    {
        // Validate request
        $validator = validator($request->all(), [
            'search' => 'nullable|string|max:255',
            'occupancy' => 'nullable|in:vacant,occupied',
            'block_name' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            $searchText = $request->input('search');
            $occupancy = $request->input('occupancy');
            $block_name = $request->input('block_name');
            $floor = $request->input('floor');

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Block::with([
                'member_info.user:id,image', // Load `image` from `users` table via member_info relation
            ])->where('society_id', $society_id);

            // Apply block name filter
            if ($request->filled('block_name')) {
                $occupiedQuery->where('name', 'like', '%' . $block_name . '%');
            }
            if ($request->filled('floor')) {
                $occupiedQuery->where('floor', 'like', '%' . $floor . '%');
            }

            // Apply occupancy filter
            if ($occupancy === 'vacant') {
                $occupiedQuery->whereDoesntHave('member_info'); // No member assigned
            } elseif ($occupancy === 'occupied') {
                $occupiedQuery->whereHas('member_info'); // At least one member assigned
            }

            // Apply search filter on `member_info` columns
            if (!empty($searchText)) {
                $occupiedQuery->whereHas('member_info', function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%')
                        ->orWhere('phone', 'like', '%' . $searchText . '%')
                        ->orWhere('aprt_no', 'like', '%' . $searchText . '%');
                });
            }

            // Paginate with a fixed limit of 10 items per page
            $members = $occupiedQuery->paginate(10);

            // Transform the members collection
            $members->getCollection()->transform(function ($block) {
                // Set image for the block itself
                // $block->image = url('storage/' . $block->user->image) ?? null;
                // unset($block->user); // Remove `user` relationship from response

                // Add `image` key inside `member_info`
                if ($block->member_info) {
                    $block->member_info->image = url('storage/' . $block->member_info->user->image)
                        ?? null;
                    unset($block->member_info->user); // Remove `user` relationship inside member_info
                }

                return $block;
            });

            return res(
                status: true,
                message: 'Members retrieved successfully',
                data: ['members' => $members],
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }






























}
