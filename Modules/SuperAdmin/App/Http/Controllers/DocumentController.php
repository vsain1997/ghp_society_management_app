<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentFile;
use App\Models\DocumentType;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class DocumentController extends Controller
{


    public function index(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Document Index Accessed', description: 'Accessing document index page');

            $validator = Validator::make($request->all(), [
                'request_type' => 'nullable|string|in:residents_request,management_request,ownself_request,admin_request',
            ]);

            if ($validator->fails()) {
                _dLog(eventType: 'error', activityName: 'Document Creation Validation Failed', description: 'Validation error during document creation: ' . $validator->errors()->first(), modelType: 'Document', modelId: null, status: 'failed');

                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $request_type = $request->input(key: 'request_type', default: 'residents_request');
            $search = $request->input(key: 'search', default: '');

            $status = 'all';
            if ($request->status == 'pending') {

                $status = 'requested';

            } elseif ($request->status == 'received') {

                $status = 'uploaded';
            }

            $query = Document::query();
            $query->with('documentType', 'requestedBy', 'requestTo');

            // user name and phone filter
            if (!empty($search)) {
                $query->whereHas('requestedBy', function ($subQuery) use ($search) {
                    $subQuery->where(function ($innerQuery) use ($search) {
                        $innerQuery->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('phone', 'LIKE', '%' . $search . '%');
                    });
                });
            }

            if ($request_type == 'residents_request') {
                //show only residents request

                // $query->where(function ($subQuery) {
                //     $subQuery->where('request_by_role', 'resident')
                //         ->orWhere('request_by_role', 'admin');
                // });
                $query->where(function ($subQuery) {
                    $subQuery->where('request_by_role', 'resident')
                        ->orWhere(function ($adminQuery) {
                            $adminQuery->where('request_by_role', 'admin')
                                ->whereNull('request_to');
                        });
                });

            } elseif ($request_type == 'management_request') {
                //show management request
                $query->where('request_by_role', 'super_admin');

            } elseif ($request_type == 'admin_request') {
                //show management request
                $query->where('request_by_role', 'admin');
                $query->whereNotNull('request_to');
            }

            // status
            if ($status != 'all') {
                $query->where('status', $status);
            } else {
                $query->where(function ($q) {
                    $q->where('status', 'requested')
                        ->orWhere('status', 'uploaded');
                });
            }

            $documents = $query->where('society_id', $selectedSociety)
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            //get all society residents
            $societyResidents = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                ->join('blocks', 'members.block_id', '=', 'blocks.id')
                ->where('members.status', 'active')
                // ->where('members.role', 'resident')
                ->where('members.society_id', $selectedSociety)
                ->get();

            // doc type
            $docTypes = DocumentType::all();


            _dLog(eventType: 'info', activityName: 'Documents Retrieved', description: 'Documents retrieved', status: 'success', severityLevel: 1);
            // dd($documents);

            return view('superadmin::document.document', [
                'documents' => $documents,
                'request_type' => $request_type,
                'search' => $search,
                'societyResidents' => $societyResidents,
                'docTypes' => $docTypes,
            ]);

        } catch (Exception $e) {
            _dLog(eventType: 'error', activityName: 'Document Index Error', description: 'Exception during document index retrieval: ' . $e->getMessage(), status: 'failed', severityLevel: 2);
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function uploadDocument(Request $request)
    {
        \DB::beginTransaction();
        try {
            // if (is_string($request->files)) {
            //     $request->merge([
            //         'files' => json_decode($request->files, true)
            //     ]);
            // }
            $validator = validator($request->all(), [
                'document_id' => 'required|integer|exists:documents,id',
                'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // Validate images and PDFs
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $document = Document::find($request->document_id);

            // Handle file uploads
            if (!$request->hasFile('files')) {
                return res(
                    status: false,
                    message: 'No files uploaded',
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }
            // if ($document->status == 'uploaded') {
            //     return res(
            //         status: false,
            //         message: 'Already uploaded',
            //         code: HTTP_UNPROCESSABLE_ENTITY
            //     );
            // }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Get the original file name
                    $originalName = $file->getClientOriginalName();

                    // Make the file name unique
                    $uniqueName = time() . '_' . $originalName;

                    // Store the file in the desired directory
                    $filePath = $file->storeAs('documents', $uniqueName, 'public');

                    // Save the file details to the database
                    DocumentFile::create([
                        'document_id' => $document->id,
                        'name' => $uniqueName,
                        'path' => $filePath,
                    ]);
                    $attchFiles[] = $uniqueName;
                }

                // $document->document_name = implode(', ', $attchFiles);
                $document->uploaded_by = auth()->id();
                $document->uploaded_by_role = auth()->user()->role;
                $document->status = 'uploaded';
                $document->save();
            }

            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            //inform notification to resident
            $checkSett = 'document_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $document) {
                $query->where('name', $checkSett)
                    ->where('user_id', $document->request_by)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $document->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;
                $notificationMessageArray = [
                    'title' => 'Document Uploaded',
                    'body' => 'Management has uploaded the document you requested',
                ];

                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            \DB::commit();

            $documentDetails = Document::with('files', 'requestedBy', 'documentType')->find($document->id);

            // Append the document type to the document
            if ($documentDetails) {
                $documentDetails->document_type = $documentDetails->documentType->type;
                unset($documentDetails->documentType);
            }

            return res(
                status: true,
                message: "File uploaded successfully",
                data: $documentDetails,
                code: HTTP_CREATED
            );

        } catch (Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(Request $request)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Document Request Started', description: 'Starting the process of creating a new document request');

            DB::beginTransaction();

            $selectedSociety = getSelectedSociety($request);

            if ($selectedSociety instanceof \Illuminate\Http\RedirectResponse) {
                return $selectedSociety; // Redirect if necessary
            }

            $loggerInfo = auth()->user();
            $logger = auth()->id();
            $logger_role = auth()->user()->role;
            $society_id = $selectedSociety;

            $validator = validator($request->all(), [
                'subject' => 'required|string|max:255',
                'request_to' => 'required|integer|exists:users,id',
                'document_type_id' => 'required|integer|exists:document_types,id',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $document = new Document();
            $document->request_to = $request->request_to;
            $document->subject = $request->subject;
            $document->document_type_id = $request->document_type_id;
            $document->description = $request->description;
            $document->status = 'requested';
            $document->request_by = $logger;
            $document->request_by_role = $logger_role;
            $document->society_id = $society_id;
            $document->save();

            // %%%%%%%%%%% send push notification :: start %%%%%%%%%%%%%%%%
            //inform notification to resident
            $checkSett = 'document_notifications';

            $user = User::whereHas('notificationSettings', callback: function ($query) use ($checkSett, $document) {
                $query->where('name', $checkSett)
                    ->where('user_id', $document->request_to)
                    ->where('status', 'enabled')
                    ->where('user_of_system', 'app')
                    ->where('society_id', $document->society_id);
            })->select('id', 'device_id')->first();

            if ($user && $user->device_id) {
                $deviceId = $user->device_id;
                $notificationMessageArray = [
                    'title' => 'Document Upload Request',
                    'body' => 'Management has requested you to upload a document',
                ];

                sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
            }
            // %%%%%%%%%%% send push notification :: end %%%%%%%%%%%%%%%%

            _dLog(eventType: 'info', activityName: 'Document Request Created', description: 'New document request created', modelType: 'Document', modelId: $document->id, status: 'success', severityLevel: 1, beforeData: null, afterData: $document->toArray(), requestData: $request->all());

            DB::commit();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Document Requested successfully',
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Document Request Creation Failed', description: 'Exception during document request creation: ' . $e->getMessage(), modelType: 'Document', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

    public function show($id)
    {
        try {

            $document = Document::with('documentType', 'files', 'society', 'uploadedBy', 'requestedBy', 'requestTo')->find($id);

            if (!$document) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Not found.'
                ]);
            }

            if ($document->request_by_role == 'resident' || ($document->request_by_role == 'admin' && empty($document->request_to))) {

                $residentOtherInfo = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                    ->join('blocks', 'members.block_id', '=', 'blocks.id')
                    ->where('members.user_id', $document->request_by)
                    ->first();
            } elseif (($document->request_by_role == 'admin' && !empty($document->request_to)) || $document->request_by_role == 'super_admin') {

                $residentOtherInfo = Member::select('members.user_id', 'members.name', 'members.aprt_no', 'members.floor_number', 'members.unit_type', 'members.phone', 'blocks.name as block_name')
                    ->join('blocks', 'members.block_id', '=', 'blocks.id')
                    ->where('members.user_id', $document->request_to)
                    ->first();
            }

            _dLog(eventType: 'info', activityName: 'Document Details Accessed', description: 'Accessing details of document ', modelType: 'Document', modelId: $id, status: 'success', severityLevel: 1);

            return view(
                'superadmin::document.details',
                compact('document', 'residentOtherInfo')
            );
        } catch (Exception $e) {

            _dLog(eventType: 'error', activityName: 'Document Details Error', description: 'Exception during document details retrieval: ' . $e->getMessage(), modelType: 'Document', modelId: null, status: 'failed', severityLevel: 2);

            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Failed please try again !'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            _dLog(eventType: 'info', activityName: 'Document Request Deletion Started', description: 'Starting the process of deleting ', modelType: 'Document', modelId: $id);

            DB::beginTransaction();
            $document = Document::find($id);
            if (!$document) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found',
                ]);
            }

            if ($document->request_by_role == 'admin' && $document->request_by == auth()->id()) {
                DB::beginTransaction();
                $document->delete();
                DB::commit();

            } else {

                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to delete',
                ]);
            }

            _dLog(eventType: 'info', activityName: 'Document Request Deleted', description: 'Document request deleted ( Title : ' . $document->title . ' ) ', modelType: 'Document', modelId: $id, status: 'success', severityLevel: 1);

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            _dLog(eventType: 'error', activityName: 'Document Request Deletion Failed', description: 'Exception during document request deletion: ' . $e->getMessage(), modelType: 'Document', modelId: $id, status: 'failed', severityLevel: 2);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed, please try again!',
            ]);
        }
    }

}
