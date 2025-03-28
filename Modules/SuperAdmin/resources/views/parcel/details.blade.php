@extends($thisModule . '::layouts.master')

@section('title', 'Parcels')

@section('content')
    <div class="right_main_body_content society_detail_page">
        <div class="head_content">
            <div class="left_head">
                <h2>
                    <a href="javascript:void(0)" onclick="history.back()">
                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.25 11.7092H20.25C20.4489 11.7092 20.6397 11.7882 20.7803 11.9289C20.921 12.0696 21 12.2603 21 12.4592C21 12.6581 20.921 12.8489 20.7803 12.9896C20.6397 13.1302 20.4489 13.2092 20.25 13.2092H5.25C5.05109 13.2092 4.86032 13.1302 4.71967 12.9896C4.57902 12.8489 4.5 12.6581 4.5 12.4592C4.5 12.2603 4.57902 12.0696 4.71967 11.9289C4.86032 11.7882 5.05109 11.7092 5.25 11.7092Z"
                                fill="black" />
                            <path
                                d="M5.56086 12.4592L11.7814 18.6782C11.9222 18.819 12.0013 19.01 12.0013 19.2092C12.0013 19.4084 11.9222 19.5994 11.7814 19.7402C11.6405 19.881 11.4495 19.9601 11.2504 19.9601C11.0512 19.9601 10.8602 19.881 10.7194 19.7402L3.96936 12.9902C3.89952 12.9205 3.84411 12.8378 3.8063 12.7466C3.76849 12.6555 3.74902 12.5578 3.74902 12.4592C3.74902 12.3605 3.76849 12.2629 3.8063 12.1717C3.84411 12.0806 3.89952 11.9979 3.96936 11.9282L10.7194 5.1782C10.8602 5.03737 11.0512 4.95825 11.2504 4.95825C11.4495 4.95825 11.6405 5.03737 11.7814 5.1782C11.9222 5.31903 12.0013 5.51004 12.0013 5.7092C12.0013 5.90836 11.9222 6.09937 11.7814 6.2402L5.56086 12.4592Z"
                                fill="black" />
                        </svg>
                        Details
                    </a>
                </h2>
            </div>
        </div>
        <div class="bg-card">
            <div class="society_details">
                <h4 class="text-primary">Parcel Info</h4>
                <ul>
                    <li>
                        <p>Parcel Id </p>
                        <h4>{{ $parcel->parcelid }}</h4>
                    </li>
                    <li>
                        <p>Parcel Name </p>
                        <h4>{{ $parcel->parcel_name }} </h4>
                    </li>
                    <li>
                        <p>No of parcel</p>
                        <h4>{{ $parcel->no_of_parcel }} </h4>
                    </li>
                    <li>
                        <p>Parcel Type</p>
                        <h4>{{ $parcel->parcel_type }} </h4>
                    </li>
                    <li>
                        <p>Delivery Name</p>
                        <h4>{{ $parcel->delivery_name }} </h4>
                    </li>
                    <li>
                        <p>Delivery Phone</p>
                        <h4>{{ $parcel->delivery_phone }} </h4>
                    </li>
                    <li>
                        <p>Delivery Option</p>
                        <h4>{{ $parcel->delivery_option }} </h4>
                    </li>
                    <li>
                        <p>Status</p>
                        <h4>{{ Str::ucfirst($parcel->handover_status) }} </h4>
                    </li>
                    <li>
                        <p>Received At</p>
                        @if (!empty($parcel->received_at))
                            <h4>{{ \Carbon\Carbon::parse($parcel->received_at)->format('d M Y g:i A') }} </h4>
                        @endif
                    </li>
                    <li>
                        <p>Delivered At</p>
                        @if (!empty($parcel->handover_at))
                            <h4>{{ \Carbon\Carbon::parse($parcel->handover_at)->format('d M Y g:i A') }} </h4>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-4 bg-card ">
            <div class="society_details">
                <h4 class="text-primary">Resident Info</h4>
                <br>
                <ul>
                    <li>
                        <p>Parcel Of </p>
                        <h4>{{ $residentOtherInfo->name }}</h4>
                    </li>
                    <li>
                        <p>Tower </p>
                        <h4>{{ $residentOtherInfo->block_name }}</h4>
                    </li>
                    <li>
                        <p>Floor </p>
                        <h4>{{ $residentOtherInfo->floor_number }}</h4>
                    </li>
                    <li>
                        <p>Property Type </p>
                        <h4>{{ $residentOtherInfo->unit_type }}</h4>
                    </li>
                    <li>
                        <p>Property Number </p>
                        <h4>{{ $residentOtherInfo->aprt_no }}</h4>
                    </li>
                    <li>
                        <p>Phone </p>
                        <h4>{{ $residentOtherInfo->phone }}</h4>
                    </li>
                </ul>
            </div>
        </div>


        <div class="mt-4 bg-card">
            <div class="society_details">
                <h4 class="text-primary">Check In Details</h4>
                <br>
                <table class="table table-responsive table-hover" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Check In At</th>
                            <th class="text-center">Check In By</th>
                            <th class="text-center">Check Out At</th>
                            <th class="text-center">Check Out By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($checkinDetails->isNotEmpty())
                            @foreach ($checkinDetails as $checkinDetail)
                                <tr>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($checkinDetail->checkin_at)->format('d M Y g:i A') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($checkinDetail->checkedInBy->checkin_by == $parcel->parcel_of)
                                            {{ 'Self Collected' }}
                                        @else
                                            {{ $checkinDetail->checkedInBy->name ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($checkinDetail->checkout_at)->format('d M Y g:i A') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($checkinDetail->checkedInBy->checkout_by == $parcel->parcel_of)
                                            {{ 'Self Collected' }}
                                        @else
                                            {{ $checkinDetail->checkedOutBy->name ?? '' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center"> No Data Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if (!empty($parcel->parcelComplaint->description))
            <div class="mt-4 bg-card ">
                <div class="society_details">
                    <h4 class="text-primary">Complaint</h4>
                    <br>
                    <h4>{{ $parcel->parcelComplaint->description }}</h4>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('footer-script')
@endpush
