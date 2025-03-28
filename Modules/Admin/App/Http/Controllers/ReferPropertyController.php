<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Sos;
use App\Models\ReferProperty;
use Illuminate\Http\Request;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class ReferPropertyController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:refer_property.view')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Refer Property Index Accessed', description: 'Accessing refer property index page');

            $selectedSociety = getSelectedSociety($request);

            if (empty($selectedSociety)) {
                $selectedSociety = auth()->user()->member->society_id;
            }

            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            $datas = ReferProperty::with('user')
                ->when($search && $search_col, function ($query) use ($search, $search_col) {
                    return $query->where($search_col, 'LIKE', '%' . $search . '%');
                })
                ->where('society_id', $selectedSociety)
                ->orderBy('id', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Refer Property list Retrieved', description: 'Refer Property list retrieved', status: 'success', severityLevel: 1);
            // dd($datas);
            return view('admin::refer_property.refer_property', [
                'refers' => $datas,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Refer Property Index Error', description: 'Exception during refer property index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $refer = ReferProperty::find($id);

            if (!$refer) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            $residentOtherInfo = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.user_id', $refer->user_id)
                ->first();

            _dLog(eventType: 'info', activityName: 'Refer Property Details Accessed', description: 'Accessing details of refer property ', modelType: 'Visitor', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'admin::refer_property.details',
                compact('refer', 'residentOtherInfo')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Refer Property Details Error', description: 'Exception during refer property details retrieval: ' . $e->getMessage(), modelType: 'ReferProperty', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }
}
