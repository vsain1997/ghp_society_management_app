<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Visitor Index Accessed', description: 'Accessing visitor index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            $datas = Visitor::with([
                'member' => function ($query) {
                    $query->select('user_id', 'id', 'name', 'aprt_no');  // Only select the 'id' and 'name' columns from the 'members' table
                },
                'lastCheckinDetail' => function ($query) {
                    $query->select('visitor_id', 'checkin_at', 'checkout_at');
                }
            ])
                ->select('visitors.id', 'visitors.user_id', 'visitors.type_of_visitor', 'visitors.visitor_name', 'visitors.phone', 'visitors.no_of_visitors', 'visitors.date', 'visitors.time', DB::raw('MAX(checkin_details.checkin_at)'), DB::raw('MAX(checkin_details.checkout_at)'))
                // ->select('visitors.*', 'checkin_details.checkin_at', 'checkin_details.checkout_at')
                ->join('checkin_details', 'visitors.id', '=', 'checkin_details.visitor_id')
                ->where('visitors.society_id', $selectedSociety)
                ->where('visitors.visitor_classification', "resident_related");

            if ($request->filled('user_id')) {
                $datas = $datas->where('visitors.user_id', $request->user_id);
            }

            if ($request->filled('checkin_date') && $request->filled('checkout_date')) {
                $checkinDate = Carbon::parse($request->checkin_date)->startOfDay();
                $checkoutDate = Carbon::parse($request->checkout_date)->endOfDay();

                // Filter for specific checkin and checkout range
                $datas = $datas->where(function ($query) use ($checkinDate, $checkoutDate) {
                    $query->where('checkin_details.checkin_at', '>=', $checkinDate)
                        ->where('checkin_details.checkout_at', '<=', $checkoutDate);
                });
            }

            if ($request->filled('search')) {
                $search = $request->input('search');

                $datas = $datas->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('visitors.visitor_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('visitors.phone', 'LIKE', '%' . $search . '%');
                    });
                });
            }

            // Remove duplicates by using 'groupBy' on 'visitors.id' to ensure each visitor is unique
            $datas = $datas->groupBy('visitors.id', 'visitors.user_id', 'visitors.type_of_visitor', 'visitors.visitor_name', 'visitors.phone', 'visitors.no_of_visitors', 'visitors.date', 'visitors.time')
                // ->orderBy('visitors.id', 'desc')
                // ->orderBy('visitors.created_at', 'desc')
                ->orderBy('visitors.date', 'desc')
                ->orderBy('visitors.time', 'desc')
                // ->orderByRaw('MAX(checkin_details.checkin_at) DESC')
                ->paginate(25);

            // Get society residents
            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                ->where('members.society_id', $selectedSociety)
                ->get();

            _dLog(eventType: 'info', activityName: 'Visitor list Retrieved', description: 'Visitor list retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::visitor.visitor', [
                'visitors' => $datas,
                'search' => $search,
                'societyResidents' => $societyResidents,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Visitor Index Error', description: 'Exception during visitor index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $visitor = Visitor::with(['visitorFeedback', 'bulkVisitors', 'checkinDetails.checkedInBy', 'checkinDetails.checkedOutBy'])->find($id);


            if (!$visitor) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            // $visitor->checkinDetails = $visitor->checkinDetails()->paginate(10);
            $checkinDetails = $visitor->checkinDetails()->orderBy('id', 'desc')->paginate(10);

            $residentOtherInfo = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.user_id', $visitor->user_id)
                ->first();


            _dLog(eventType: 'info', activityName: 'Visitor Details Accessed', description: 'Accessing details of visitor ', modelType: 'Visitor', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::visitor.details',
                compact('visitor', 'residentOtherInfo', 'checkinDetails')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Visitor Details Error', description: 'Exception during visitor details retrieval: ' . $e->getMessage(), modelType: 'Visitor', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function indexOtherVisitor(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Visitor (Other) Index Accessed', description: 'Accessing visitor (Other) index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $status = $request->input(key: 'status', default: 'active');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            $datas = Visitor::with([
                'lastCheckinDetail' => function ($query) {
                    $query->select('visitor_id', 'checkin_at', 'checkout_at');
                }
            ])
                ->select('visitors.id', 'visitors.type_of_visitor', 'visitors.visitor_name', 'visitors.phone', 'visitors.no_of_visitors', 'visitors.date', 'visitors.time', DB::raw('MAX(checkin_details.checkin_at)'), DB::raw('MAX(checkin_details.checkout_at)'))
                // ->select('visitors.*', 'checkin_details.checkin_at', 'checkin_details.checkout_at')
                ->join('checkin_details', 'visitors.id', '=', 'checkin_details.visitor_id')
                ->where('visitors.society_id', $selectedSociety)
                ->where('visitors.visitor_classification', 'other');


            if ($request->filled('checkin_date') && $request->filled('checkout_date')) {
                $checkinDate = Carbon::parse($request->checkin_date)->startOfDay();
                $checkoutDate = Carbon::parse($request->checkout_date)->endOfDay();

                // Filter for specific checkin and checkout range
                $datas = $datas->where(function ($query) use ($checkinDate, $checkoutDate) {
                    $query->where('checkin_details.checkin_at', '>=', $checkinDate)
                        ->where('checkin_details.checkout_at', '<=', $checkoutDate);
                });
            }

            if ($request->filled('search')) {
                $search = $request->input('search');

                $datas = $datas->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('visitors.visitor_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('visitors.phone', 'LIKE', '%' . $search . '%');
                    });
                });
            }

            // Remove duplicates by using 'groupBy' on 'visitors.id' to ensure each visitor is unique
            $datas = $datas->groupBy('visitors.id', 'visitors.type_of_visitor', 'visitors.visitor_name', 'visitors.phone', 'visitors.no_of_visitors', 'visitors.date', 'visitors.time')
                // ->orderBy('visitors.id', 'desc')
                // ->orderBy('checkin_details.checkin_at', 'desc')
                ->orderBy('visitors.date', 'desc')
                ->orderBy('visitors.time', 'desc')
                // ->orderByRaw('MAX(checkin_details.checkin_at) DESC')
                ->paginate(25);


            _dLog(eventType: 'info', activityName: 'Visitor (Other) list Retrieved', description: 'Visitor (Other) list retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::visitor.visitor-other', [
                'visitors' => $datas,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Visitor (Other) Index Error', description: 'Exception during visitor index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function showOtherVisitor($id)
    {
        try {

            $visitor = Visitor::with(['visitorFeedback', 'bulkVisitors', 'checkinDetails.checkedInBy', 'checkinDetails.checkedOutBy'])->find($id);


            if (!$visitor) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            // $visitor->checkinDetails = $visitor->checkinDetails()->paginate(10);
            $checkinDetails = $visitor->checkinDetails()->orderBy('id', 'desc')->paginate(10);

            _dLog(eventType: 'info', activityName: 'Visitor (Other) Details Accessed', description: 'Accessing details of visitor (Other) ', modelType: 'Visitor', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::visitor.details-other',
                compact('visitor', 'checkinDetails')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Visitor (Other) Details Error', description: 'Exception during visitor details retrieval: ' . $e->getMessage(), modelType: 'Visitor', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }
}
