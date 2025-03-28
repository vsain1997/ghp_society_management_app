<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\DocumentType;
use App\Models\User;
use App\Notifications\DynamicNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DocumentController extends Controller
{

    public function elements()
    {
        try {
            $docTypes = DocumentType::all();
            // Check if not found
            if ($docTypes->isEmpty()) {
                return res(
                    status: false,
                    message: "Resources are not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $data = [
                'documents_types' => $docTypes,
            ];
            return res(
                status: true,
                message: "Form dropdowns retrieved successfully",
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

    public function sendRequest(Request $request)
    {
        \DB::beginTransaction();
        try {
            $loggerInfo = auth()->user();
            $logger = auth()->id();
            $logger_role = auth()->user()->role;
            $society_id = $loggerInfo->society_id;

            $validator = validator($request->all(), [
                'subject' => 'required|string|max:255',
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
            $document->subject = $request->subject;
            $document->document_type_id = $request->document_type_id;
            $document->description = $request->description;
            $document->status = 'requested';
            $document->request_by = $logger;
            $document->request_by_role = $logger_role;
            $document->society_id = $society_id;
            $document->save();

            $documentDetails = Document::with('files', 'requestedBy', 'requestedByMember', 'documentType', 'society')->find($document->id);

            // Append the document type to the document
            if ($documentDetails) {
                $documentDetails->document_type = $documentDetails->documentType->type;
                unset($documentDetails->documentType);
            }

            // ================================================
            // Send notifications to Admin (Panel)
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'document.';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($documentDetails) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $documentDetails->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);

            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                $checkSettings = 'document_notifications';
                $checkForUser = $notifyUser->id;
                $checkForDevice = 'panel';
                $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                if ($isSettingEnabled) {

                    $data = [
                        'via' => ['database'],
                        'database' => [
                            'title' => 'New Document Request',
                            'body' => "Document request from " . $documentDetails->requestedByMember->name ,
                            'model' => 'Document',
                            'model_id' => $documentDetails->id,
                            'society_name' => $documentDetails->society->name,
                            'society_id' => $documentDetails->society_id,
                        ],
                    ];

                    $notifyUser->notify(new DynamicNotification($data));
                }
            }
            // ================================================

            \DB::commit();

            return res(
                status: true,
                message: "Request sent successfully",
                data: $documentDetails,
                code: HTTP_CREATED
            );

        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function requestsCount()
    {
        try {
            $loggerInfo = auth()->user();
            $logger_role = $loggerInfo->role;
            $logger = auth()->id();
            $society_id = $loggerInfo->society_id;

            // incoming request ================================
            $query = Document::query();
            if ($logger_role == 'admin') {
                //for admin app user ,request can come from super_admin
                $query->where('request_by_role', 'super_admin');

            } elseif ($logger_role == 'resident') {
                //for resident app user ,request can come from super_admin or admin

                $query->where(function ($subQuery) {
                    $subQuery->where('request_by_role', 'admin')
                        ->orWhere('request_by_role', 'super_admin');
                });

            }
            //requested to me
            $query->where('request_to', $logger)
                ->where('status', 'requested')
                ->where('society_id', $society_id);

            $getIncomingRequestCount = $query->count();

            // outgoing request (logger request)================================
            $query = Document::query();
            if ($logger_role == 'admin') {
                $query->where('request_by_role', 'admin');

            } elseif ($logger_role == 'resident') {

                $query->where('request_by_role', 'resident');
            }

            //request by me
            $query->where('request_by', $logger)
                ->where('status', 'requested')
                ->where('society_id', $society_id);

            $getOutgoingRequestCount = $query->count();

            $data = [
                'incoming_request_count' => $getIncomingRequestCount,
                'outgoing_request_count' => $getOutgoingRequestCount,
            ];

            return res(
                status: true,
                message: "Request count retrieved successfully",
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

    public function outgoingRequests(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'filter_type' => 'required|string|in:all,pending,received',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $loggerInfo = auth()->user();
            $logger_role = $loggerInfo->role;
            $logger = auth()->id();
            $society_id = $loggerInfo->society_id;

            // outgoing request (logger request)================================
            $query = Document::query();
            $query->with('files', 'requestedBy', 'documentType');
            if ($logger_role == 'admin') {
                $query->where('request_by_role', 'admin');

            } elseif ($logger_role == 'resident') {

                $query->where('request_by_role', 'resident');
            }

            // tab wise data
            if ($request->filter_type == 'pending') {

                $query->where('status', 'requested');

            } elseif ($request->filter_type == 'received') {

                $query->where('status', 'uploaded');
            }

            //request by me
            $query->where('request_by', $logger)
                ->where('society_id', $society_id);

            $getOutgoingRequestList = $query->orderBy('id', 'desc')
                ->paginate(25);

            // Append document_type to each document
            $getOutgoingRequestList->getCollection()->transform(function ($document) {
                $document->document_type = $document->documentType->type;
                unset($document->documentType);
                return $document;
            });

            $data = [
                'outgoing_requests' => $getOutgoingRequestList,
            ];

            return res(
                status: true,
                message: "Outgoing Requests List retrieved successfully",
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

    public function incomingRequests(Request $request)
    {
        try {

            $validator = validator($request->all(), [
                'filter_type' => 'required|string|in:all,pending,received',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $loggerInfo = auth()->user();
            $logger_role = $loggerInfo->role;
            $logger = auth()->id();
            $society_id = $loggerInfo->society_id;

            // outgoing request (logger request)================================
            $query = Document::query();
            $query->with('files', 'requestedBy', 'documentType');
            if ($logger_role == 'admin') {
                //for admin app user ,request can come from super_admin
                $query->where('request_by_role', 'super_admin');

            } elseif ($logger_role == 'resident') {
                //for resident app user ,request can come from super_admin or admin

                $query->where(function ($subQuery) {
                    $subQuery->where('request_by_role', 'admin')
                        ->orWhere('request_by_role', 'super_admin');
                });

            }
            // tab wise data
            if ($request->filter_type == 'pending') {

                $query->where('status', 'requested');

            } elseif ($request->filter_type == 'received') {

                $query->where('status', 'uploaded');
            }

            //requested to me
            $query->where('request_to', $logger)
                ->where('society_id', $society_id);

            $getIncomingRequestList = $query->orderBy('id', 'desc')
                ->paginate(25);

            // Append document_type to each document
            $getIncomingRequestList->getCollection()->transform(function ($document) {
                $document->document_type = $document->documentType->type;
                unset($document->documentType);
                return $document;
            });

            $data = [
                'incoming_requests' => $getIncomingRequestList,
            ];

            return res(
                status: true,
                message: "Incoming Requests List retrieved successfully",
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

    public function details($id)
    {
        try {
            $document = Document::with('files', 'requestedBy', 'documentType')
                ->find($id);

            if (!$document) {
                return res(
                    status: false,
                    message: "No documents found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Append the document type to the document
            if ($document) {
                $document->document_type = $document->documentType->type;
                unset($document->documentType);
            }

            // Return the response in JSON format
            $data = [
                'document' => $document,
            ];
            return res(
                status: true,
                message: "Document retrieved successfully",
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

    public function getFiles($id)
    {
        try {
            $document = DocumentFile::where('document_id', $id)
                ->get();

            if (!$document) {
                return res(
                    status: false,
                    message: "No document files found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'files' => $document,
            ];
            return res(
                status: true,
                message: "Document files retrieved successfully",
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
            if ($document->status == 'uploaded') {
                return res(
                    status: false,
                    message: 'Already uploaded',
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

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

            $documentDetails = Document::with('files', 'requestTo', 'requestedBy', 'requestedByMember', 'documentType', 'society')->find($document->id);

            // ================================================
            // Send notifications to Admin (Panel)
            //get superAdmins
            $superAdmins = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();
            //get admin
            $checkPermission = 'document.';
            // Filter users based on the new prefix
            $admins = User::whereHas('member', function ($query) use ($documentDetails) {
                $query->where('role', 'admin')
                    ->where('status', 'active')
                    ->where('society_id', $documentDetails->society_id);
            })->get()->filter(function ($admin) use ($checkPermission) {
                return $admin->getAllPermissions()->pluck('name')->contains(function ($permission) use ($checkPermission) {
                    return \Str::startsWith($permission, $checkPermission);
                });
            });

            // Combine superAdmins and admins into one collection
            $allAdminSuperAdminUsers = $superAdmins->concat($admins);

            foreach ($allAdminSuperAdminUsers as $key => $notifyUser) {

                // send to who requests
                if ($notifyUser->id == $documentDetails->request_by) {

                    $checkSettings = 'document_notifications';
                    $checkForUser = $notifyUser->id;
                    $checkForDevice = 'panel';
                    $isSettingEnabled = isNotificationSettingEnabled($checkSettings, $checkForUser, $checkForDevice);
                    if ($isSettingEnabled) {

                        $data = [
                            'via' => ['database'],
                            'database' => [
                                'title' => 'Requested Document Uploaded',
                                'body' => "Document uploaded by " . $documentDetails->requestTo->name,
                                'model' => 'Document',
                                'model_id' => $documentDetails->id,
                                'society_name' => $documentDetails->society->name,
                                'society_id' => $documentDetails->society_id,
                            ],
                        ];

                        $notifyUser->notify(new DynamicNotification($data));
                    }
                }
            }
            // ================================================

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

        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy($id)
    {
        try {
            // Find the document by ID
            $document =
                Document::where('request_by_role', auth()
                    ->user()->role)
                    ->where('request_by', auth()->id())
                    ->find($id);

            // Check if the document exists
            if (!$document) {
                return res(
                    status: false,
                    message: "No document found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Delete the document
            $document->delete();

            return res(
                status: true,
                message: "Document deleted successfully",
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

