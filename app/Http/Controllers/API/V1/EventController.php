<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Validate search input if provided
        $validator = validator($request->all(), [
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
            $query = Event::upcoming()
                ->searchByStatus($status)
                ->searchBySociety($society_id);

            // Filter by search text
            if ($search && $searchCol) {
                $query->where($searchCol, 'LIKE', '%' . $search . '%');
            }

            // Order events by upcoming date and time
            $events = $query->orderBy('date', 'asc')  // First order by date
                ->orderBy('time', 'asc')  // Then by time on the same day
                ->paginate(25);

            // Check if events are found
            if ($events->isEmpty()) {
                return res(
                    status: false,
                    message: "No events found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'events' => $events,
            ];
            return res(
                status: true,
                message: "Events retrieved successfully",
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
            $query = Event::upcoming()
                ->searchByStatus($status)
                ->searchBySociety($society_id)
                ->searchById($id);

            $event = $query->get();

            // Check if event are found
            if ($event->isEmpty()) {
                return res(
                    status: false,
                    message: "No event found!",
                    code: HTTP_NOT_FOUND
                );
            }

            // Return the response in JSON format
            $data = [
                'event' => $event,
            ];
            return res(
                status: true,
                message: "Event details retrieved successfully",
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
