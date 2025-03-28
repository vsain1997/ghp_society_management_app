@extends('superadmin::layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="right_main_body_content members_page ">
        <div class="overview-page">
            <ul>
                <li>
                    {{-- <a href="{{ url($thisModule . '/society/resident-units') }}"> --}}
                    <a>
                        <div class="unit-img-box">
                            <img src="img/solar_layers-bold.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Total Properties</h4>
                            <h3>
                                {{ $society_units }}
                            </h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ url($thisModule . '/member') }}">
                        <div class="unit-img-box">
                            <img src="img/solar_layers-bold.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Total Occupied</h4>
                            <h3>
                                {{ $members }}
                            </h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ url($thisModule . '/staff') }}">
                        <div class="unit-img-box">
                            <img src="img/solar_layers-bold.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Total Staffs</h4>
                            <h3>
                                {{ $staffs }}
                            </h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ url($thisModule . '/service-provider') }}">
                        <div class="unit-img-box">
                            <img src="img/Labor-Hands-Action--Streamline-Ultimate.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Service Providers</h4>
                            <h3>{{ $service_providers }}</h3>
                        </div>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="{{ url($thisModule . '/complaint') }}">
                        <div class="unit-img-box">
                            <img src="img/Task-Finger-Show--Streamline-Ultimate.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>New Complaints</h4>
                            <h3>{{ $complaints }}</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ url($thisModule . '/visitor') }}">
                        <div class="unit-img-box">
                            <img src="img/Task-Finger-Show--Streamline-Ultimate.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Today Visitors</h4>
                            <h3>{{ $todayVisitors }}</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ url($thisModule . '/poll') }}">
                        <div class="unit-img-box">
                            <img src="img/Task-Finger-Show--Streamline-Ultimate.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Ongoing Polls</h4>
                            <h3>{{ $currentPolls }}</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ url($thisModule . '/notice') }}">
                        <div class="unit-img-box">
                            <img src="img/Task-List-Pin--Streamline-Ultimate.png" alt="">
                        </div>
                        <div class="unit-text-box">
                            <h4>Total Notice
                                {{-- document Requests --}}
                            </h4>
                            <h3>{{ $notices }}</h3>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="complaints-sos">
            <div class="row">
                <div class="col-lg-6">
                    <div class="complaints-box">
                        <div class="com-viewall">
                            <h4>Notice Board</h4>
                            <a href="{{ url($thisModule . '/notice') }}">View All</a>
                        </div>
                        <div class="table-responsive notic-board-tab">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th class="text-center">Date & Time</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($notice_data && !$notice_data->isEmpty())
                                        @foreach ($notice_data as $notice)
                                            <tr>
                                                <td>{{ $notice->title }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($notice->date)->format('d M Y') }}
                                                    {{ \Carbon\Carbon::parse($notice->time)->format('g:i A') }}
                                                </td>

                                                <td class="text-center">

                                                    <div class="actions">
                                                        <a class="view"
                                                            href="{{ route($thisModule . '.notice.details', ['id' => $notice->id]) }}"
                                                            id="{{ $notice->id }}">
                                                            <svg width="15" height="15" viewBox="0 0 15 15"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center"> No Data Found </td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="complaints-box">
                        <div class="com-viewall">
                            <h4>Events</h4>
                            <a href="{{ url($thisModule . '/event') }}">View All</a>
                        </div>
                        <div class="table-responsive notic-board-tab">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th class="text-center">Date & Time</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($event_data && !$event_data->isEmpty())
                                        @foreach ($event_data as $event)
                                            <tr>
                                                <td>{{ $event->title }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                                    {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}
                                                </td>

                                                <td class="text-center">

                                                    <div class="actions">
                                                        <a class="view"
                                                            href="{{ route($thisModule . '.event.details', ['id' => $event->id]) }}"
                                                            id="{{ $event->id }}">
                                                            <svg width="15" height="15" viewBox="0 0 15 15"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center"> No Data Found </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="complaints-sos">

            <div class="row">
                <div class="col-lg-6 mt-4">
                    <div class="complaints-box">
                        <div class="com-viewall">
                            <h4>Complaints</h4>
                            <a href="{{ url($thisModule . '/complaint') }}">View All</a>
                        </div>
                        <div class="table-responsive complaints-table">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Complaint by</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Property</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Complaint Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($complaint_data && !$complaint_data->isEmpty())
                                        @foreach ($complaint_data as $complaint)
                                            @php
                                                // 'requested','assigned','in_progress','done','cancelled'

                                                if ($complaint->status == 'requested') {
                                                    $stts_text = 'New';
                                                    $stts_class = 'up-status-btn';
                                                } elseif ($complaint->status == 'assigned') {
                                                    $stts_text = 'Assigned';
                                                    $stts_class = 'grey-status-btn';
                                                } elseif ($complaint->status == 'in_progress') {
                                                    $stts_text = 'In Progress';
                                                    $stts_class = 'yellow-status-btn';
                                                } elseif ($complaint->status == 'done') {
                                                    $stts_text = 'Resolved';
                                                    $stts_class = 'green-status-btn';
                                                } elseif ($complaint->status == 'cancelled') {
                                                    $stts_text = 'Cancelled';
                                                    $stts_class = 'exp-status-btn';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $complaint->complaintBy->name }}</td>
                                                <td>{{ $complaint->serviceCategory->name }}</td>
                                                <td class="text-center">
                                                    {{ $complaint->block_name }}-{{ $complaint->aprt_no }}</td>
                                                <td class="text-center {{ $stts_class }}">
                                                    <button class="text-uppercase">{{ $stts_text }}</button>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($complaint->complaint_at)->format('d M Y') }}
                                                </td>

                                                <td class="text-center">
                                                    <div class="actions">
                                                        <a href="{{ route($thisModule . '.complaint.details', ['id' => $complaint->id]) }}"
                                                            id="{{ $complaint->id }}">
                                                            <svg width="15" height="15" viewBox="0 0 15 15"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>

                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center"> No Data Found </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-4">
                    <div class="sos-box">
                        <div class="sos-fire">
                            <h3>SOS</h3>
                            <a href="{{ url($thisModule . '/sos') }}">View All</a>
                        </div>
                        <ul>
                            @if ($sos_data && !$sos_data->isEmpty())
                                @foreach ($sos_data as $sos)
                                    <li>
                                        <div class="fire-img-text">
                                            <div class="acti-list heading-data">

                                                <div class="fire-content">
                                                    <h4>{{ $sos->sosCategory->name }}</h4>
                                                    <p>{{ $sos->area }}</p>
                                                </div>
                                            </div>
                                            <div class="acti-list time-acti">
                                                {{-- <div class="fire-img"> --}}
                                                {{-- <img src="img/ph_fire.png" alt=""> --}}
                                                {{-- <i class="fa-regular fa-clock text-danger"></i> --}}
                                                {{-- </div> --}}
                                                <div class="fire-content">
                                                    <h4>Time</h4>
                                                    <p>
                                                        {{ \Carbon\Carbon::parse($sos->date . ' ' . $sos->time)->format('d M Y g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="call-img"><a href="tel:{{ $sos->user->phone }}">
                                                    <img src="img/phone.png" alt="">
                                                </a>
                                            </div>
                                        </div>
                                        <p>
                                            @if (strlen($sos->description) > 50)
                                                {{ substr($sos->description, 0, 47) . '...' }}
                                            @else
                                                {{ $sos->description }}
                                            @endif
                                        </p>
                                    </li>
                                @endforeach
                            @else
                                <div class="fire-img-text">
                                    <div class="acti-list heading-data">

                                        <div class="fire-content">
                                            <h4>&nbsp;</h4>
                                            <p>No Data Found</p>
                                        </div>
                                    </div>
                                    <div class="acti-list time-acti">
                                        <div class="fire-content">
                                            <h4>&nbsp;</h4>
                                            <h4>&nbsp;</h4>
                                            <h4>&nbsp;</h4>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="complaints-sos">
            <div class="row">
                <div class="col-lg-6 mt-4">
                    <div class="complaints-box">
                        <div class="com-viewall">
                            <h4>Today Visitors</h4>
                            <a href="{{ url($thisModule . '/visitor') }}">View All</a>
                        </div>
                        <div class="table-responsive complaints-table">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Visitee</th>
                                        <th class="text-center">Property</th>
                                        <th class="text-center">Visitor</th>
                                        <th class="text-center">Visiting Time</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($visitor_data && !$visitor_data->isEmpty())
                                        @foreach ($visitor_data as $vdata)
                                            <tr>
                                                <td class="text-center">{{ $vdata->member->name }}</td>
                                                <td class="text-center">
                                                    {{ $vdata->member->block_name }}-{{ $vdata->member->aprt_no }}
                                                </td>
                                                <td class="text-center">{{ $vdata->visitor_name }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($vdata->date . ' ' . $vdata->time)->format('g:i A') }}
                                                </td>

                                                <td class="text-center">
                                                    <div class="actions">
                                                        <a href="{{ route($thisModule . '.visitor.details', ['id' => $vdata->id]) }}"
                                                            id="{{ $vdata->id }}">
                                                            <svg width="15" height="15" viewBox="0 0 15 15"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>

                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center"> No Data Found </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mt-4">
                    <div class="complaints-box">
                        <div class="com-viewall">
                            <h4>Ongoing Polls</h4>
                            <a href="{{ url($thisModule . '/poll') }}">View All</a>
                        </div>
                        <div class="table-responsive complaints-table">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Title</th>
                                        <th class="text-center">Top Voted</th>
                                        <th class="text-center">Total Votes</th>
                                        <th class="text-center">End Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($currentPoll_data && !$currentPoll_data->isEmpty())
                                        @foreach ($currentPoll_data as $pollInfo)
                                            <tr>
                                                <td class="text-center">{{ $pollInfo->title }}</td>
                                                <td class="text-center">
                                                    @if ($pollInfo->options->isNotEmpty() && $pollInfo->votes_count > 0)
                                                        <div>
                                                            <p><strong>Option:</strong>
                                                                {{ $pollInfo->options->first()->option_text }}</p>
                                                            <p><strong>Votes:</strong>
                                                                {{ $pollInfo->options->first()->votes_count }}</p>
                                                        </div>
                                                    @else
                                                        {{ '-' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $pollInfo->votes_count }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($pollInfo->end_date)->format('d M Y') }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="actions">
                                                        <a href="{{ route($thisModule . '.poll.details', ['id' => $pollInfo->id]) }}"
                                                            id="{{ $pollInfo->id }}">
                                                            <svg width="15" height="15" viewBox="0 0 15 15"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                                    stroke="#8077F5" stroke-width="1.25"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>

                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center"> No Data Found </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
