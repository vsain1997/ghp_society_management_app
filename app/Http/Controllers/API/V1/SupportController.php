<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Society;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    //
    public function getContact()
    {
        try {
            // Get society ID of the authenticated user
            $society_id = auth()->user()->society_id;

            // Retrieve the member (admin) details
            $member = Member::where('society_id', $society_id)->where('role', 'admin')->first();

            // Check if the member exists
            if (!$member) {
                return res(
                    status: false,
                    message: "Assigned admin not found",
                    code: HTTP_NOT_FOUND
                );
            }

            // Prepare contact support data
            $support = [
                'phone' => $member->phone,
                'email' => $member->email,
            ];

            // Prepare response data
            $data = [
                'contact' => $support,
            ];

            // Return success response with data
            return res(
                status: true,
                message: "Data retrieved successfully",
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
