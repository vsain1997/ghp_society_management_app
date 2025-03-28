<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Society;
use App\Models\Complaint;
use App\Models\Staff;
use App\Models\ServiceProviders;
use App\Models\Event;
use App\Models\Document;
use App\Models\Poll;
use App\Models\ReferProperty;
use App\Models\Sos;
use App\Models\TradeProperty;
use App\Models\Visitor;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');
            _dLog(eventType: 'info', activityName: 'Dashboard Accessed', description: 'Accessing dashboard index page');

            $society_id = getSelectedSociety($request);

            if ($society_id instanceof \Illuminate\Http\RedirectResponse) {
                return $society_id; // Redirect if necessary
            }

            $blocks = Block::where('society_id', $society_id)->count();
            $society_units = $blocks;

            // total resident unit occupied
            $members = Member::where('status', 'active')->where('society_id', $society_id)->count();

            $service_providers = ServiceProviders::where('society_id', $society_id)->count();

            // staffs-----------------------------------------------
            $staffs = Staff::where('society_id', $society_id)->count();
            // visitor-----------------------------------------------
            $todayVisitors = Visitor::where(function ($q) use ($now) {
                $q->whereDate('date', '=', $now->toDateString())
                    ->orWhere('visiting_frequency', '=', 'Daily');
            })
                ->where('society_id', $society_id)
                ->count();

            $visitor_query = Visitor::with([
                'member' => function ($visitor_query) {
                    $visitor_query->select(
                        'members.user_id',
                        'members.name',
                        'members.aprt_no',
                        'members.floor_number',
                        'members.unit_type',
                        'members.phone',
                        'blocks.name as block_name'
                    )->join('blocks', 'members.block_id', '=', 'blocks.id');
                }
            ])
                ->where(function ($q) use ($now) {
                    $q->whereDate('date', '=', $now->toDateString())
                        ->orWhere('visiting_frequency', '=', 'Daily');
                })
                ->where('society_id', $society_id)
                ->where('visitor_classification', 'resident_related')
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc');
            $visitor_data = $visitor_query->limit(4)->get();

            // polls-----------------------------------------------
            $currentPolls = Poll::where('end_date', '>=', $now->toDateString())
                ->where('visibility', 'published')
                ->where('society_id', $society_id)
                ->count();

            $currentPoll_data = Poll::with([
                'options' => function ($query) {
                    // Include vote counts for each option
                    // $query->withCount('votes');
                    $query->withCount('votes')->orderBy('votes_count', 'desc');
                }
            ])
                ->withCount('votes')
                ->where('end_date', '>=', $now->toDateString())
                ->where('visibility', 'published')
                ->where('society_id', $society_id)
                ->orderBy('end_date', 'desc')
                ->limit(3)
                ->get();

            // notice-----------------------------------------------
            $notices = Notice::where('society_id', $society_id)->count();
            $notice_data = Notice::where('society_id', $society_id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            // event-----------------------------------------------
            $event_data = Event::where('society_id', $society_id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            // complaints-----------------------------------------------
            $complaints = Complaint::where('society_id', $society_id)->count();
            $complaint_data = Complaint::with('society', 'complaintBy', 'serviceCategory', 'staff', 'assignedTo')
                ->where('society_id', $society_id)
                ->orderBy('id', 'desc')
                ->limit(2)
                ->get();
            // sos-----------------------------------------------
            $sos_data = Sos::with('sosCategory', 'block', 'user')
                ->where('society_id', $society_id)
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();


            _dLog(eventType: 'info', activityName: 'Dashboard Data Retrieved', description: 'Dashboard Data retrieved', status: 'success', severityLevel: 1);

            return view('superadmin::dashboard.dashboard', [
                'society_units' => $society_units,
                'members' => $members,
                'complaints' => $complaints,
                'notices' => $notices,
                'service_providers' => $service_providers,
                'sos_data' => $sos_data,
                'notice_data' => $notice_data,
                'event_data' => $event_data,
                'complaint_data' => $complaint_data,
                'staffs' => $staffs,
                'todayVisitors' => $todayVisitors,
                'currentPolls' => $currentPolls,
                'visitor_data' => $visitor_data,
                'currentPoll_data' => $currentPoll_data,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Dashboard Index Error', description: 'Exception during dashboard index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }
}
