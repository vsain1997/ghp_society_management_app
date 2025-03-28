<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Parcel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class ParcelController extends Controller
{

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Parcel Index Accessed', description: 'Accessing parcel index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $status = $request->input(key: 'status', default: 'pending');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            $datas = Parcel::with([
                'member' => function ($query) {
                    $query->select('user_id', 'id', 'name', 'aprt_no');  // Only select the 'id' and 'name' columns from the 'members' table
                }
            ])
                ->select('parcels.*')
                // ->select('parcels.*', 'checkin_details.checkin_at', 'checkin_details.checkout_at')
                // ->join('checkin_details', 'parcels.id', '=', 'checkin_details.parcel_id')
                ->where('society_id', $selectedSociety);

            if ($request->filled('user_id')) {
                $datas = $datas->where('parcel_of', $request->user_id);
            }

            if (!empty($status)) {
                $datas = $datas->where('handover_status', $status);

                $dateTypeName = '';
                if ($status == 'pending') {
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $fromDate = Carbon::parse($request->from_date)->startOfDay();
                        $toDate = Carbon::parse($request->to_date)->endOfDay();

                        $datas = $datas->where(function ($query) use ($fromDate, $toDate) {
                            $query->where('date', '>=', $fromDate)
                                ->where('date', '<=', $toDate);
                        });
                    }
                } elseif ($status == 'received') {
                    $dateTypeName = 'Received';
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $fromDate = Carbon::parse($request->from_date)->startOfDay();
                        $toDate = Carbon::parse($request->to_date)->endOfDay();

                        // Filter for specific checkin and checkout range
                        $datas = $datas->where(function ($query) use ($fromDate, $toDate) {
                            $query->where('received_at', '>=', $fromDate)
                                ->where('received_at', '<=', $toDate);
                        });
                    }
                } elseif ($status == 'delivered') {
                    $dateTypeName = 'Delivered';
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $fromDate = Carbon::parse($request->from_date)->startOfDay();
                        $toDate = Carbon::parse($request->to_date)->endOfDay();

                        // Filter for specific checkin and checkout range
                        $datas = $datas->where(function ($query) use ($fromDate, $toDate) {
                            $query->where('handover_at', '>=', $fromDate)
                                ->where('handover_at', '<=', $toDate);
                        });
                    }
                }

                $datas = $datas->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('parcelid', 'LIKE', '%' . $search . '%')
                            ->orWhere('parcel_name', 'LIKE', '%' . $search . '%');
                    });
                });
            }
            if ($status == 'pending') {

                $datas = $datas->orderBy('date', 'asc')
                    ->orderBy('time', 'desc');

            } elseif ($status == 'received') {

                $datas = $datas->orderBy('received_at', 'desc');

            } elseif ($status == 'delivered') {

                $datas = $datas->orderBy('handover_at', 'desc');
            }

            $datas = $datas->paginate(25);

            // Get society residents
            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $selectedSociety)
                ->get();

            _dLog(eventType: 'info', activityName: 'Parcel list Retrieved', description: 'Parcel list retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::parcel.parcel', [
                'parcels' => $datas,
                'search' => $search,
                'societyResidents' => $societyResidents,
                'dateTypeName' => $dateTypeName,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Parcel Index Error', description: 'Exception during parcel index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $parcel = Parcel::with(['checkinDetail.checkedInBy', 'checkinDetail.checkedOutBy', 'parcelComplaint'])->find($id);


            if (!$parcel) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            // $parcel->checkinDetail = $parcel->checkinDetail()->paginate(10);
            $checkinDetails = $parcel->checkinDetail()->orderBy('id', 'desc')->paginate(10);

            $residentOtherInfo = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.user_id', $parcel->parcel_of)
                ->first();


            _dLog(eventType: 'info', activityName: 'Parcel Details Accessed', description: 'Accessing details of parcel ', modelType: 'Parcel', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::parcel.details',
                compact('parcel', 'residentOtherInfo', 'checkinDetails')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Parcel Details Error', description: 'Exception during parcel details retrieval: ' . $e->getMessage(), modelType: 'Parcel', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }
}
