<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Member;
use App\Models\Sos;
use Illuminate\Http\Request;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class SocietyController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:society.resident_units_view')->only(['index', 'memberList']);
    }

    public function memberList(Request $request)
    {
        // Validate request
        $validator = validator($request->all(), [
            'search' => 'nullable|string|max:255',
            'occupancy' => 'nullable|in:vacant,occupied',
            'tower' => 'nullable|string|max:255',
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
            _dLog(eventType: 'info', activityName: 'Society Properties Accessed', description: 'Accessing society properties page');

            // Fetch the authenticated user
            $logger = auth()->user();
            $society_id = $logger->society_id;

            $searchText = $request->input('search');
            $occupancy = $request->input('occupancy');
            $block_name = $request->input('tower');
            $floor = $request->input('floor');

            // Fetch the active members associated with the selected blocks
            $occupiedQuery = Block::with([
                'member_info.user:id,image', // Load `image` from `users` table via member_info relation
            ])->where('society_id', $society_id);

            // Apply block name filter
            if ($request->filled('tower')) {
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
                if ($occupancy === 'occupied') {
                    $occupiedQuery->whereHas('member_info', function ($query) use ($searchText) {
                        $query->where('name', 'like', '%' . $searchText . '%')
                            ->orWhere('phone', 'like', '%' . $searchText . '%')
                            ->orWhere('aprt_no', 'like', '%' . $searchText . '%');
                    });
                } else {
                    $occupiedQuery->where('property_number', 'like', '%' . $searchText . '%');
                }
            }

            // Paginate with a fixed limit of 10 items per page
            $members = $occupiedQuery->paginate(25);

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

            $blocks = Block::where('society_id', $society_id)
                ->select('name')
                ->distinct()
                ->orderBy('name')
                ->get();
            $floors = Block::where('society_id', $society_id)
                ->where('name', 'Like', '%' . $block_name . '%')
                ->select('floor')
                ->distinct()
                ->orderBy('floor')
                ->get();

            return view('admin::society.resident_unit', [
                'datas' => $members,
                'search' => $searchText,
                'occupancy' => $occupancy,
                'tower' => $block_name,
                'floor' => $floor,
                'floors' => $floors,
                'blocks' => $blocks,
            ]);

        } catch (Exception $e) {

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function show($id)
    {
        try {

            $data = Member::select('members.id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'members.email', 'blocks.name as block_name', 'blocks.unit_size as unit_size')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->find($id);

            if (!$data) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Resident Unit Details Accessed', description: 'Accessing details of resident unit ', modelType: 'Member', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::society.resident_unit_details',
                compact('data')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Resident Unit Details Error', description: 'Exception during resident unit details retrieval: ' . $e->getMessage(), modelType: 'Member', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function getBlocksOLD(Request $request)
    {
        // Validate the request
        $request->validate([
            'society_id' => 'required|exists:societies,id',
        ]);

        // Fetch blocks based on society_id
        $blocks = Block::with('society')->where('society_id', $request->society_id)->get();

        // Check if blocks were found
        if ($blocks->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No blocks found on this society.',
            ]);
        }

        // Return the blocks as a JSON response
        return response()->json([
            'status' => 'success',
            'blocks' => $blocks,
        ]);
    }

    public function getBlocks(Request $request)
    {
        // Validate the request
        $request->validate([
            'society_id' => 'required|exists:societies,id',
        ]);

        // Fetch blocks based on society_id
        // $blocks = Block::with('society')->where('society_id', $request->society_id)->get();

        if ($request->block_id == null) {

            $getUniqueBlocks = DB::table('blocks')
                ->select(DB::raw('MAX(id) as id'), 'name', DB::raw('GROUP_CONCAT(property_number) as property_numbers'))
                ->where('society_id', $request->society_id)
                ->whereNull('deleted_at')
                ->groupBy('name')
                ->orderBy('name', 'asc')
                ->get();

            // Get the first block name from the result
            $selectedBlock = $getUniqueBlocks->isEmpty() ? null : $getUniqueBlocks->first()->name;

            if ($selectedBlock == null) {

                return response()->json([
                    'status' => 'success',
                    'blocks' => "Not Found",
                ]);
            }

            // Step 2: Get all block IDs where blocks.name = selectedBlock
            $selectedIds = DB::table('blocks')
                ->where('name', $selectedBlock)
                ->where('society_id', $request->society_id)
                ->whereNull('deleted_at')
                ->pluck('id'); // Get an array of block IDs
            // dd($selectedIds);

        } else {

            $getBlock = DB::table('blocks')
                ->select('name')
                ->where('id', $request->block_id)
                ->where('society_id', $request->society_id)
                ->whereNull('deleted_at')
                ->first(); // Get an array of block IDs

            $selectedIds = DB::table('blocks')
                ->where('name', $getBlock->name)
                ->where('society_id', $request->society_id)
                ->whereNull('deleted_at')
                ->pluck('id'); // Get an array of block IDs

        }
        // Step 3: Get unmatched block IDs where member.block_id not in selectedIds
        if (is_numeric($request->user_id) && $request->user_id > 0) {
            $unmatchedIds = DB::table('members')
                ->whereIn('block_id', $selectedIds)
                ->where('society_id', $request->society_id)
                ->whereNull('deleted_at')
                ->where('user_id', '!=', $request->user_id) // Exclude matching user_id
                ->pluck('block_id'); // Get the unmatched block IDs
        } else {
            $unmatchedIds = DB::table('members')
                ->whereIn('block_id', $selectedIds)
                ->where('society_id', $request->society_id)
                ->whereNull('deleted_at')
                ->pluck('block_id'); // Get the unmatched block IDs
        }

        $unmatchedSelectedIds = $selectedIds->diff($unmatchedIds);
        // dd($unmatchedSelectedIds);

        // Step 4: Get blocks where blocks.id is in the unmatched IDs
        $availableProperties = DB::table('blocks')
            ->whereIn('id', $unmatchedSelectedIds)
            ->where('society_id', $request->society_id)
            ->whereNull('deleted_at')
            ->select('id', 'name', 'property_number')
            ->orderBy('id', 'desc')  // Order by 'id' in descending order
            ->get();

        // Return the blocks as a JSON response
        return response()->json([
            'status' => 'success',
            'getUniqueBlocks' => $getUniqueBlocks ?? [],
            // 'selectedBlock' => $selectedBlock,
            'availableProperties' => $availableProperties,
        ]);
    }

    public function getBlockFloor(Request $request)
    {
        try {
            $block_name = $request->block_name;
            $society_id = auth()->user()->society_id;

            $blockArr = array();
            $distinctFloors = Block::where('name', 'like', '%' . $block_name . '%')
                ->where('society_id', $society_id)
                ->distinct()
                ->pluck('floor')
                ->toArray();
            $distinctFloors = array_map(function ($floor) {
                return ['name' => $floor];
            }, $distinctFloors);

            $blockCh['name'] = $block_name;
            $blockCh['floors'] = $distinctFloors;
            array_push($blockArr, $blockCh);

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



}
