<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\TradePropertiesFile;
use Illuminate\Http\Request;
use App\Models\Society;
use App\Models\TradeProperty;
use App\Models\Bhk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class TradeController extends Controller
{
    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Property Listing Index Accessed', description: 'Accessing property listing index page');

            $selectedSociety = getSelectedSociety($request);
            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $type = $request->input(key: 'type', default: 'rent');
            $search = $request->input(key: 'search', default: '');
            $search_col = $request->input(key: 'search_for', default: '');

            // $datas = TradeProperty::when($search && $search_col, function ($query) use ($search, $search_col) {
            //     return $query->where($search_col, 'LIKE', '%' . $search . '%');
            // })
            //     ->select('trade_properties.*', 'blocks.name as block_name')
            //     ->join('blocks', 'trade_properties.block_id', '=', 'blocks.id')
            //     ->where('society_id', $selectedSociety)
            //     ->orderBy('id', 'desc')
            //     ->paginate(25);
            $datas = TradeProperty::select('trade_properties.*', 'blocks.name as block_name')
                ->join('blocks', 'trade_properties.block_id', '=', 'blocks.id') // Move join before where
                ->where('trade_properties.type', $type)
                ->where('trade_properties.society_id', $selectedSociety) // Ensure prefix
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('trade_properties.name', 'LIKE', '%' . $search . '%')
                            ->orWhere('trade_properties.phone', 'LIKE', '%' . $search . '%');
                    });
                })
                ->orderBy('trade_properties.id', 'desc') // Use explicit table reference
                ->paginate(25);


            _dLog(eventType: 'info', activityName: 'Property listing Retrieved', description: 'Property listing retrieved', status: 'success', severityLevel: 1);
            // dd($datas);
            return view('superadmin::propertyListing.propertyListing', [
                'propertyListings' => $datas,
                'search' => $search,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Property listing Index Error', description: 'Exception during property listing index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $propertyListing = TradeProperty::with('files')
                ->select('trade_properties.*', 'blocks.name as block_name')
                ->join('blocks', 'trade_properties.block_id', '=', 'blocks.id')
                ->find($id);

            if (!$propertyListing) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Property listing details Accessed', description: 'Accessing details of property listing ', modelType: 'TradeProperty', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::propertyListing.details',
                compact('propertyListing')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Property listing Details Error', description: 'Exception during property listing details retrieval: ' . $e->getMessage(), modelType: 'TradeProperty', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }
}
