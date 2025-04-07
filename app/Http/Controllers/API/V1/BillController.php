<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\RazorpayPayments;
use Illuminate\Http\Request;

class BillController extends Controller
{


    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'user_id' => 'required|exists:users,id',
                'service_id' => 'required|exists:bill_services,id',
                'bill_type' => 'required|in:my_bill,maintenance',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date|after_or_equal:' . now()->setTimezone('Asia/Kolkata')->toDateString(),
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Create new visitor
            $bill = Bill::create([
                ...$request->all(),
                'status' => 'unpaid',
                'created_by' => auth()->id(),
                'society_id' => auth()->user()->society_id,
            ]);

            \DB::commit();
            $data = [
                'bill' => $bill,
            ];

            return res(
                status: true,
                message: "Successfully created",
                data: $data,
                code: HTTP_CREATED
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function index(Request $request)
    {
        try {
            // Validate input
            $validator = validator($request->all(), [
                'bill_type' => 'required|in:paid,unpaid,all',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user_id = auth()->id();
            $searchBillType = $request->input('bill_type');

            if ($searchBillType == 'all') {
                $query = Bill::with('service')
                    ->searchByResident($user_id);
            } else {
                $searchStatus = $searchBillType; //unpaid or paid
                $query = Bill::with('service')
                    ->searchByStatus($searchStatus)
                    ->searchByResident($user_id);
            }
            // Order by due date
            $bills = $query->orderBy('due_date', 'asc')
                ->paginate(25);

            $totalUnpaidAmount = Bill::searchByStatus('unpaid')
                ->searchByResident($user_id)
                ->sum('amount');
            $totalPaidAmount = Bill::searchByStatus('paid')
                ->searchByResident($user_id)
                ->sum('amount');

            // Check if visitors are found
            if ($bills->isEmpty()) {

                // Return the response in JSON format
                $data = [
                    'total_unpaid_amount' => $totalUnpaidAmount,
                    'total_paid_amount' => $totalPaidAmount,
                    'bills' => null,
                ];
                return res(
                    status: false,
                    message: "No bills found!",
                    data: $data,
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'total_unpaid_amount' => $totalUnpaidAmount,
                'total_paid_amount' => $totalPaidAmount,
                'bills' => $bills,
            ];
            return res(
                status: true,
                message: "Bill retrieved successfully",
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
            $user_id = auth()->id();
            // Start building the query
            $query = Bill::with('service')
                ->searchByResident($user_id)
                ->searchById($id);

            $bill = $query->get();

            // Check if visitor are found
            if ($bill->isEmpty()) {
                return res(
                    status: false,
                    message: "No bill found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'bill' => $bill,
            ];
            return res(
                status: true,
                message: "Bill Details retrieved successfully",
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

    /**
     * Billing Details
     * @param Request $request
     * @return mixed
    */
    public function paymentDetails(Request $request)
    {
        try {

            return $request;

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
