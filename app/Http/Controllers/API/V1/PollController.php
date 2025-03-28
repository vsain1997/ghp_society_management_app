<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class PollController extends Controller
{

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            $validator = validator($request->all(), [
                'title' => 'required|string|max:255',
                'visibility' => 'required|in:draft,published',
                'end_date' => 'required|date',
                'options.*' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $society_id = auth()->user()->society_id;
            $created_by = auth()->id();
            $poll = Poll::create([
                'title' => $request->input('title'),
                'visibility' => $request->input('visibility'),
                'end_date' => $request->input('end_date'),
                'society_id' => $society_id,
                'created_by' => $created_by,
            ]);

            foreach ($request->options as $option) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $option,
                ]);
            }

            \DB::commit();
            return res(
                status: true,
                message: "created successfully",
                data: $poll,
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

    // Show all polls
    public function index()
    {
        $society_id = auth()->user()->society_id; // Corrected the property name
        $visibility = 'published';

        try {
            $today = now()->setTimezone('Asia/Kolkata')->toDateString(); // Only the date part
            $twoDaysAgo = now()->subDays(2)->toDateString(); // Date 2 days ago

            // Fetch polls that have not ended (end_date > today)
            $activePolls = Poll::with([
                'options' => function ($query) {
                    // Include vote counts for each option
                    $query->withCount('votes');
                }
            ])
                ->where('end_date', '>=', $today)
                ->searchByVisibility($visibility)
                ->searchBySociety($society_id)
                ->orderBy('end_date', 'asc')
                ->paginate(25);

            // Fetch last 2 polls that ended 2 days before
            $recentExpiredPolls = Poll::with([
                'options' => function ($query) {
                    // Include vote counts for each option
                    $query->withCount('votes');
                }
            ])
                ->where('end_date', '>=', $twoDaysAgo)
                ->searchByVisibility($visibility)
                ->searchBySociety($society_id)
                ->orderBy('end_date', 'desc')
                ->limit(2)
                ->get();

            // return response()->json($recentExpiredPolls);
            // Merge both active and recently expired polls
            $polls = $activePolls->merge($recentExpiredPolls);

            if ($polls->isEmpty()) {
                return res(
                    status: false,
                    message: 'No polls found',
                    code: HTTP_OK
                );
            }

            // Get current user ID
            $userId = auth()->id();

            // Prepare response data with voting results
            $polls->transform(function ($poll) use ($userId, $today) {
                // Check if the user has voted for the poll
                $hasVoted = PollVote::where('poll_id', $poll->id)
                    ->where('user_id', $userId)
                    ->exists();

                // Check if the poll has ended
                $isExpired = $poll->end_date < $today;

                // Calculate the remaining days until the poll ends
                // $daysRemaining = now()->diffInDays(\Carbon\Carbon::parse($poll->end_date), false);
                // $daysRemaining = now()->setTimezone('Asia/Kolkata')->diffInDays(\Carbon\Carbon::parse($poll->end_date), false);

                $daysRemaining = now()->setTimezone('Asia/Kolkata')->startOfDay()->diffInDays(\Carbon\Carbon::parse($poll->end_date)->startOfDay(), false);


                // Prepare the end message based on remaining days
                // $endMsg = $daysRemaining > 0
                //     ? "Voting will end in $daysRemaining day(s)"
                //     : 'Voting has ended';
                // $daysRemaining = $poll->end_date->diffInDays($today, false);
                $endMsg = $daysRemaining > 1
                    ? "Voting will end in $daysRemaining days"
                    : ($daysRemaining == 1
                        ? "Voting will end tomorrow"
                        : ($daysRemaining == 0
                            ? "Voting will end today"
                            : "Voting has ended"));

                // echo $daysRemaining;
                return [
                    'id' => $poll->id,
                    'title' => $poll->title,
                    'options' => $poll->options,
                    'end_date' => $poll->end_date,
                    'has_voted' => $hasVoted,
                    'is_expired' => $isExpired,
                    'end_msg' => $endMsg, // Added end message here
                ];

            });

            return res(
                status: true,
                message: 'Polls retrived successfully',
                data: $polls,
                code: HTTP_OK
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

    public function vote(Request $request, $poll_id)
    {
        \DB::beginTransaction();
        try {
            $validator = validator($request->all(), [
                'poll_option_id' => 'required|exists:poll_options,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $poll = Poll::find($poll_id);
            if (!$poll) {
                return res(
                    status: false,
                    message: 'Poll not found',
                    code: HTTP_OK
                );
            }

            // Check if the user has already voted for this poll
            $existingVote = PollVote::where('poll_id', $poll_id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingVote) {
                // Update the existing vote
                // $existingVote->update(['poll_option_id' => $request->poll_option_id]);
                // \DB::commit();
                // return res(
                //     status: true,
                //     message: 'Vote updated successfully',
                //     code: HTTP_OK
                // );
                return res(
                    status: false,
                    message: 'You have already voted for this poll',
                    code: HTTP_CONFLICT
                );
            } else {
                // Create a new vote
                PollVote::create([
                    'poll_id' => $poll_id,
                    'poll_option_id' => $request->poll_option_id,
                    'user_id' => auth()->id(),
                ]);
                \DB::commit();
                return res(
                    status: true,
                    message: 'Vote casted successfully',
                    code: HTTP_CREATED
                );
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    public function voteold(Request $request, $poll_id)
    {
        \DB::beginTransaction();
        try {
            $validator = validator($request->all(), [
                'poll_option_id' => 'required|exists:poll_options,id',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $poll = Poll::find($poll_id);

            if (!$poll) {
                return res(
                    status: false,
                    message: 'Poll not found',
                    code: HTTP_OK
                );
            }

            PollVote::create([
                'poll_id' => $poll_id,
                'poll_option_id' => $request->poll_option_id,
                'user_id' => auth()->id(),
            ]);

            \DB::commit();
            return res(
                status: true,
                message: 'Vote casted successfully',
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


}

