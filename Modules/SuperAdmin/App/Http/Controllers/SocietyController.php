<?php

namespace Modules\SuperAdmin\App\Http\Controllers;
use App\Imports\SocietyImport;
use App\Http\Controllers\Controller;
use App\Models\Sos;
use App\Models\Complaint;
use App\Models\Member;
use App\Models\SocietyContact;
use App\Models\TradeProperty;
use Illuminate\Http\Request;
use App\Models\Society;
use App\Models\Block;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SocietyController extends Controller
{
    public function store(Request $request)
    {
        try {
            superAdminLog('info', 'start::store');
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'sname' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pin' => 'required|string|max:255',
                'societyContact' => 'nullable|numeric|min:10',
                'societyEmail' => 'nullable|email',
                'reg' => 'nullable|string',
                'societyTypeSelect' => 'nullable|string',
                'societyArea' => 'required|numeric',
                'totalTowers' => 'required|numeric',
                'statusSelect' => 'required|string|in:active,inactive',
                // 'floors' => 'required|integer',
                // 'floorUnits' => 'required|integer',
                // 'assignAdmin' => 'required|integer',
                // 'bname' => 'required|array',
                // 'bname.*' => 'required|string|max:255',
                // 'totalFloors' => 'required|array',
                // 'totalFloors.*' => 'required|integer',
                
                // 'property_number' => 'required|array',
                // 'property_number.*.*' => 'required|string',
                // 'property_floor' => 'required|array',
                // 'property_floor.*.*' => 'required',
                // 'property_type' => 'required|array',
                // 'property_type.*.*' => 'required|string',
                // 'ownership' => 'required|array',
                // 'ownership.*.*' => 'required|string',
                // 'unit_size' => 'required|array',
                // 'unit_size.*.*' => 'required|numeric',
                // 'bhk' => 'nullable|array',
                // 'bhk.*.*' => 'nullable',
                // 'block_id' => 'nullable|array',
                // 'block_id.*.*' => 'nullable',
                
                'emr_name' => 'required|array',
                'emr_name.*' => 'required|string',
                'emr_designation' => 'required|array',
                'emr_designation.*' => 'required|string',
                'emr_phone' => 'required|array',
                'emr_phone.*' => 'required|numeric|min:10',
                'emr_id' => 'nullable|array',
                'emr_id.*' => 'integer',
            ]);
            // echo "validator fails = ";dd($validator->fails());
            // dd("Adfas");
            // dd($request->all());

            if ($validator->fails()) {
                superAdminLog('error', 'Validation failed: ' . $validator->errors()->first());
                return redirect()->back()
                    ->withInput()
                    ->with([
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ]);
            }
            
            $insData = [
                'name' => $request->input('sname'),
                'location' => $request->input('location'),
                'floors' => 0,
                'status' => $request->input('statusSelect'),
                'floor_units' => 0,
                'member_id' => NULL,//$request->input('assignAdmin'),
                
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'pin' => $request->input('pin'),
                'contact' => $request->input('societyContact'),
                'email' => $request->input('societyEmail'),
                'registration_num' => $request->input('reg'),
                'type' => $request->input('societyTypeSelect'),
                'total_area' => $request->input('societyArea'),
                'total_towers' => $request->input('totalTowers'),
                'amenities' => implode(',', $request->input('amenities'))
            ];
            $society = Society::create($insData);
            
            superAdminLog('info', 'Society::created');
            $societyId = $society->id;
            
            $bnameArray = $request->input('bname');
            $totalUnitsArray = $request->input('totalUnits');
            $unitTypeArray = $request->input('unit_type');
            $unitQtyArray = $request->input('unit_qty');
            
            $totalFloorsArray = $request->input('totalFloors');
            $property_numberArray = $request->input('property_number');
            $property_floorArray = $request->input('property_floor');
            $property_typeArray = $request->input('property_type');
            $ownershipArray = $request->input('ownership');
            $unitSizeArray = $request->input('unit_size');
            $bhkArray = $request->input('bhk');
            
            if($bnameArray){
                foreach ($bnameArray as $blockKey => $blockName) {
                    
                    foreach ($property_numberArray[$blockKey] as $unitIndex => $property_number) {
                        Block::create([
                            'name' => $blockName,
                            'total_floor' => $totalFloorsArray[$blockKey],
                            'property_number' => $property_number,
                            'floor' => $property_floorArray[$blockKey][$unitIndex],
                            'unit_type' => $property_typeArray[$blockKey][$unitIndex],
                            'ownership' => $ownershipArray[$blockKey][$unitIndex],
                            'unit_size' => !empty($unitSizeArray[$blockKey][$unitIndex]) ? $unitSizeArray[$blockKey][$unitIndex] : '',
                            'bhk' => !empty($bhkArray[$blockKey][$unitIndex]) ? $bhkArray[$blockKey][$unitIndex] : '',
                            'society_id' => $societyId,
                            'total_units' => 0,
                        ]);
                    }
                }
                superAdminLog('info', 'Block::created');
            }
            
            
            // emergency contact
            $emrNameArray = $request->input('emr_name');
            $emrDesignationArray = $request->input('emr_designation');
            $emrPhoneArray = $request->input('emr_phone');
            
            foreach ($emrNameArray as $emKey => $emrName) {
                SocietyContact::create([
                    'name' => $emrName,
                    'designation' => $emrDesignationArray[$emKey],
                    'phone' => $emrPhoneArray[$emKey],
                    'society_id' => $societyId,
                ]);
            }
            superAdminLog('info', 'SocietyContact::created');
            DB::commit();
            superAdminLog('info', 'end::store');
            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Added successfully'
            ]);
            // dd('eee');
            
        } catch (\Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $society = Society::with('blocks', 'society_contacts', 'assigned_admin')->find($id);

            if (!$society) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }
            return view(
                'superadmin::society.details',
                compact('society')
            );
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $society = Society::with('blocks', 'society_contacts')->find($id);

            // $existingMemberIds = Society::pluck('member_id');

            // Retrieve admins who are not in the Society model
            $admins = Member::where('role', 'admin')
                ->where('society_id', $id)
                // ->whereNotIn('id', $existingMemberIds)
                ->get();

            if (!$society) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found'
                ]);
            }
            return response()->json([
                'status' => 'success',
                'data' => $society,
                'admins' => $admins
            ]);

        } catch (\Exception $e) {
            // 'error' => $e->getMessage(),
            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function update(Request $request, $societyId)
    {
        $validator = Validator::make($request->all(), [
            'sname' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin' => 'required|string|max:255',
            'societyContact' => 'nullable|numeric|min:10',
            'societyEmail' => 'nullable|email',
            'reg' => 'nullable|string',
            'societyTypeSelect' => 'nullable|string',
            'societyArea' => 'required|numeric',
            'totalTowers' => 'required|numeric',
            'statusSelect' => 'required|string|in:active,inactive',
            // 'floors' => 'required|integer|min:1',
            // 'floorUnits' => 'required|integer|min:1',
            // 'assignAdmin' => 'required|integer|min:1',
            // 'bname' => 'required|array',
            // 'bname.*' => 'required|string|max:255',
            // 'totalFloors' => 'required|array',
            // 'totalFloors.*' => 'required|integer',

            // 'property_number' => 'required|array',
            // 'property_number.*.*' => 'required|string',
            // 'property_floor' => 'required|array',
            // 'property_floor.*.*' => 'required',
            // 'property_type' => 'required|array',
            // 'property_type.*.*' => 'required|string',
            // 'ownership' => 'required|array',
            // 'ownership.*.*' => 'required|string',
            // 'unit_size' => 'required|array',
            // 'unit_size.*.*' => 'required|numeric',
            // 'bhk' => 'nullable|array',
            // 'bhk.*.*' => 'nullable',
            // 'block_id' => 'nullable|array',
            // 'block_id.*.*' => 'nullable',

            'emr_name' => 'required|array',
            'emr_name.*' => 'required|string',
            'emr_designation' => 'required|array',
            'emr_designation.*' => 'required|string',
            'emr_phone' => 'required|array',
            'emr_phone.*' => 'required|numeric|min:10',
            'emr_id' => 'nullable|array',
            'emr_id.*' => 'integer',
        ]);

        if ($validator->fails()) {
            superAdminLog('error', $validator->errors()->first());
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        DB::beginTransaction();

        try {
            superAdminLog('info', 'Entered update method');

            $society = Society::find($societyId);
            if (!$society) {
                superAdminLog('error', 'Society not found');
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Not found',
                    ]
                );
            }

            $society->update([
                'name' => $request->input('sname'),
                'location' => $request->input('location'),
                'floors' => $request->input('floors'),
                'status' => $request->input('statusSelect'),
                'floor_units' => $request->input('floorUnits'),
                'member_id' => NULL,//$request->input('assignAdmin'),

                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'pin' => $request->input('pin'),
                'contact' => $request->input('societyContact'),
                'email' => $request->input('societyEmail'),
                'registration_num' => $request->input('reg'),
                'type' => $request->input('societyTypeSelect'),
                'total_area' => $request->input('societyArea'),
                'total_towers' => $request->input('totalTowers'),
                'amenities' => !empty($request->input('amenities')) && is_array($request->input('amenities'))
                    ? implode(',', $request->input('amenities'))
                    : null
            ]);
            superAdminLog('info', 'society updated');
            // Get the existing blocks for this society
            $existingBlocks = Block::where('society_id', $societyId)->get()->keyBy('id');

            

            $bnameArray = $request->input('bname');
            $blockIdArray = $request->input('block_id');
            $totalUnitsArray = $request->input('totalUnits');
            $unitTypeArray = $request->input('unit_type');
            $unitQtyArray = $request->input('unit_qty');
            
            $totalFloorsArray = $request->input('totalFloors');
            $property_numberArray = $request->input('property_number');
            $property_floorArray = $request->input('property_floor');
            $property_typeArray = $request->input('property_type');
            $ownershipArray = $request->input('ownership');
            $unitSizeArray = $request->input('unit_size');
            $bhkArray = $request->input('bhk');
            $blockIdsToKeep = [];
            if($bnameArray){
                foreach ($bnameArray as $blockKey => $blockName) {
                    if (isset($blockIdArray[$blockKey])) {
                        
                        if (1 === 1) {
                            foreach ($property_numberArray[$blockKey] as $unitIndex => $property_number) {
                                Block::updateOrCreate(
                                    ['id' => $blockIdArray[$blockKey][$unitIndex]],
                                    [
                                        'name' => $blockName,
                                        'total_floor' => $totalFloorsArray[$blockKey],
                                        'property_number' => $property_number,
                                        'floor' => $property_floorArray[$blockKey][$unitIndex],
                                        'unit_type' => $property_typeArray[$blockKey][$unitIndex],
                                        'ownership' => $ownershipArray[$blockKey][$unitIndex],
                                        'unit_size' => !empty($unitSizeArray[$blockKey][$unitIndex]) ? $unitSizeArray[$blockKey][$unitIndex] : '',
                                        'bhk' => !empty($bhkArray[$blockKey][$unitIndex]) ? $bhkArray[$blockKey][$unitIndex] : '',
                                        'society_id' => $societyId,
                                        'total_units' => 0,
                                    ]
                                );
    
                                if (!empty($blockIdArray[$blockKey][$unitIndex])) {
                                    $blockIdsToKeep[] = $blockIdArray[$blockKey][$unitIndex];
    
                                    $existsBlockInMember = Member::where('block_id', $blockIdArray[$blockKey][$unitIndex])->exists();
    
                                    if ($existsBlockInMember) {
                                        $member = Member::where('block_id', $blockIdArray[$blockKey][$unitIndex])->first();
                                        if ($member) {
                                            $member->floor_number = $property_floorArray[$blockKey][$unitIndex];
                                            $member->unit_type = $property_typeArray[$blockKey][$unitIndex];
                                            $member->aprt_no = $property_number;
                                            $member->save();
                                        }
    
                                    }
                                    // ------------ Complaints
                                    Complaint::whereIn('block_id', array_column($blockIdArray, $unitIndex))
                                        ->chunk(100, function ($complaints) use ($blockIdArray, $blockKey, $unitIndex, $blockName, $property_floorArray, $property_typeArray, $property_number) {
                                            foreach ($complaints as $complaint) {
                                                $complaint->block_name = $blockName;
                                                $complaint->floor_number = $property_floorArray[$blockKey][$unitIndex];
                                                $complaint->unit_type = $property_typeArray[$blockKey][$unitIndex];
                                                $complaint->aprt_no = $property_number;
                                                $complaint->save();
                                            }
                                        });
    
                                    // ------------ SOS
                                    Sos::whereIn('block_id', array_column($blockIdArray, $unitIndex))
                                        ->chunk(100, function ($sosRecords) use ($blockIdArray, $blockKey, $unitIndex, $property_floorArray, $property_typeArray, $property_number) {
                                            foreach ($sosRecords as $sos) {
                                                $sos->floor = $property_floorArray[$blockKey][$unitIndex];
                                                $sos->unit_type = $property_typeArray[$blockKey][$unitIndex];
                                                $sos->unit_no = $property_number;
                                                $sos->save();
                                            }
                                        });
    
                                    // ------------ Trade Property (Rent/Sell)
                                    TradeProperty::whereIn('block_id', array_column($blockIdArray, $unitIndex))
                                        ->chunk(100, function ($tradeProperties) use ($blockIdArray, $blockKey, $unitIndex, $property_floorArray, $property_typeArray, $property_number, $bhkArray, $unitSizeArray) {
                                            foreach ($tradeProperties as $tradeProperty) {
                                                $tradeProperty->floor = $property_floorArray[$blockKey][$unitIndex];
                                                $tradeProperty->unit_type = $property_typeArray[$blockKey][$unitIndex];
                                                $tradeProperty->unit_number = $property_number;
                                                $tradeProperty->bhk = !empty($bhkArray[$blockKey][$unitIndex]) ? $bhkArray[$blockKey][$unitIndex] : '';
                                                $tradeProperty->area = !empty($unitSizeArray[$blockKey][$unitIndex]) ? $unitSizeArray[$blockKey][$unitIndex] : '';
                                                $tradeProperty->save();
                                            }
                                        });
                                }
                            }
                        }
                    }
                }
            }

            $blockIdsToDelete = $existingBlocks->keys()->diff($blockIdsToKeep);

            Block::whereIn('id', $blockIdsToDelete)->delete();
            Block::whereIn('id', $blockIdsToDelete)->forceDelete();
            superAdminLog('info', 'society updated');//

            
            $existingSocietyContacts = SocietyContact::where('society_id', $societyId)->get()->keyBy('id');

            $emrNameArray = $request->input('emr_name');
            $emrDesignationArray = $request->input('emr_designation');
            $emrPhoneArray = $request->input('emr_phone');
            $emrIdArray = $request->input('emr_id');

            $emrIdsToKeep = [];

            foreach ($emrNameArray as $emKey => $emrName) {
                // Check if IDs exist for this block
                if (isset($emrIdArray[$emKey])) {
                    foreach ($emrIdArray[$emKey] as $emrId) {
                        //Update existing
                        if (isset($existingSocietyContacts[$emrId])) {
                            // If already, update it
                            SocietyContact::updateOrCreate(
                                ['id' => $emrId],
                                [
                                    'name' => $emrName,
                                    'designation' => $emrDesignationArray[$emKey],
                                    'phone' => $emrPhoneArray[$emKey],
                                    'society_id' => $societyId,
                                ]
                            );
                        } else {
                            //If ID doesn't exist, create a new
                            SocietyContact::create([
                                'name' => $emrName,
                                'designation' => $emrDesignationArray[$emKey],
                                'phone' => $emrPhoneArray[$emKey],
                                'society_id' => $societyId,
                            ]);
                        }
                        $emrIdsToKeep[] = $emrId;
                    }
                } else {
                    //Create new if no IDs provided
                    SocietyContact::create([
                        'name' => $emrName,
                        'designation' => $emrDesignationArray[$emKey],
                        'phone' => $emrPhoneArray[$emKey],
                        'society_id' => $societyId,
                    ]);
                }
            }

            $emrIdsToDelete = $existingSocietyContacts->keys()->diff($emrIdsToKeep);
            SocietyContact::destroy($emrIdsToDelete);
            superAdminLog('info', 'society updated');

            DB::commit();
            superAdminLog('info', 'final::society updated successfully');
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Updated successfully',
                ]
            );
        } catch (\Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

  

    public function importFile(Request $request): JsonResponse{
        $request->validate([
            'importedFile' => 'required|mimes:csv,xlsx,xls|max:5048',
        ]);

        if (!$request->hasFile('importedFile')) {
            return response()->json([
                'status' => false,
                'message' => 'File upload failed.',
            ], 400);
        }

        $societyId = $request->input('societyId');
        $totalTower = Society::where('id', $societyId)->value('total_towers');
        $unitType = Society::where('id', $societyId)->value('type');

        $import = new SocietyImport($societyId, $totalTower, $unitType);

        try {
            Excel::import($import, $request->file('importedFile'));
            $importResponse = session('import_response');      
      dd($importResponse);
            $skippedBlocks = $import->skippedBlocks ?? 0; // custom property
            $totalImported = $import->total_blocks ?? 0;
            dd($totalImported);
            return response()->json([
                'status' => true,
                'message' => 'Import successful.',
                'imported' => $totalImported,
                'skipped_blocks' => $skippedBlocks,
            ]);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return response()->json([
                'status' => false,
                'message' => 'Import failed due to validation errors.',
                'errors' => $errors,
            ], 422);
        } catch (\Exception $e) {
            Log::error('Import Exception: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred during import.',
            ], 500);
        }
    }




    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $society = Society::find($id);
            if (!$society) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Not found'
                ]);
            }
            // Soft delete
            $society->blocks()->delete();
            $society->society_contacts()->delete();
            $society->delete();
            // $block->forceDelete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again'
            ]);
        }
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
            ->whereNull('deleted_at')
            ->where('society_id', $request->society_id)
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

    public function changeStatus($id, $status)
    {
        try {
            superAdminLog('info', 'start::changeStatus-society');
            $society = Society::find($id);
            if (!$society) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }
            $society->status = $status;
            $society->save();

            superAdminLog('info', 'end<success>::changeStatus-society');
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
            ]);
        } catch (\Exception $e) {
            superAdminLog('error', 'Exception::', $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed please try again!'
            ]);
        }
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
            // $society_id = $logger->society_id;

            $society_id = getSelectedSociety($request);

            if ($society_id instanceof \Illuminate\Http\RedirectResponse) {
                return $society_id; // Redirect if necessary
            }

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

            return view('superadmin::society.resident_unit', [
                'datas' => $members,
                'search' => $searchText,
                'occupancy' => $occupancy,
                'tower' => $block_name,
                'floor' => $floor,
                'floors' => $floors,
                'blocks' => $blocks,
            ]);

        } catch (\Exception $e) {

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
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
