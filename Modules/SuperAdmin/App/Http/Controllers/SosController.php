<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class SosController extends Controller
{

    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Sos Index Accessed', description: 'Accessing sos index page');

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $search = $request->input(key: 'search', default: '');

            $datas = Sos::with('sosCategory', 'block', 'user')
                ->when($search, function ($query) use ($search) {
                    return $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('phone', 'LIKE', '%' . $search . '%');
                    });
                });

            if ($request->filled('status')) {
                $datas = $datas->searchByStatus($request->status);
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate = Carbon::parse($request->to_date)->endOfDay();

                // Filter for specific checkin and checkout range
                $datas = $datas->where(function ($query) use ($fromDate, $toDate) {
                    $query->where('date', '>=', $fromDate)
                        ->where('date', '<=', $toDate);
                });
            }
            $datas = $datas->where('society_id', $selectedSociety)
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            _dLog(eventType: 'info', activityName: 'Sos list Retrieved', description: 'Sos list retrieved', status: 'success', severityLevel: 1);
            // dd($datas);
            return view('superadmin::sos.sos', [
                'soss' => $datas,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Sos Index Error', description: 'Exception during sos index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $sos = Sos::with('sosCategory', 'block', 'user', 'acknowledgedBy')->find($id);

            if (!$sos) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }


            _dLog(eventType: 'info', activityName: 'Sos Details Accessed', description: 'Accessing details of sos ', modelType: 'Sos', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::sos.details',
                compact('sos')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Sos Details Error', description: 'Exception during sos details retrieval: ' . $e->getMessage(), modelType: 'Sos', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }
}
