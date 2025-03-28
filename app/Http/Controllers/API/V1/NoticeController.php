<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    //
    public function index(Request $request)
    {
        // Validate search input if provided
        $validator = validator($request->all(), [
            // 'society_id' => 'required|exists:societies,id|string|max:255',
            'search' => 'nullable|string|max:255',
            'search_for' => 'nullable|string|in:title,description',
        ]);

        if ($validator->fails()) {
            return res(
                status: false,
                message: $validator->errors()->first(),
                code: HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $logger = auth()->user();
            $society_id = $logger->society_id;

            $status = 'active';
            $search = $request->input('search', '');
            $searchCol = $request->input('search_for', 'title');

            // Start building the query
            $query = Notice::searchByStatus($status)
                ->searchBySociety($society_id);

            // Filter by search text
            if ($search && $searchCol) {
                $query->where($searchCol, 'LIKE', '%' . $search . '%');
            }

            // Pagination
            $notices = $query->orderBy('id', 'desc')->paginate(25);

            // Check if notices are found
            if ($notices->isEmpty()) {
                return res(
                    status: false,
                    message: "No notices found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'notices' => $notices,
            ];
            return res(
                status: true,
                message: "Notice retrived successfully",
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
            $logger = auth()->user();
            $society_id = $logger->society_id;
            $status = 'active';
            // Start building the query
            $query = Notice::searchByStatus($status)
                ->searchBySociety($society_id)
                ->searchById($id);

            $notice = $query->get();

            // Check if notice are found
            if ($notice->isEmpty()) {
                return res(
                    status: false,
                    message: "No notice found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'notice' => $notice,
            ];
            return res(
                status: true,
                message: "Notice details retrived successfully",
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
