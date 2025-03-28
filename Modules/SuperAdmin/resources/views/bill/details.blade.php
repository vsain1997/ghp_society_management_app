@extends($thisModule . '::layouts.master')

@section('title', 'Billing')

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
                <ul>
                    <li>
                        <p>Society </p>
                        <h4>{{ $bill->society->name }}</h4>
                    </li>
                    <li>
                        <p>Bill For </p>
                        <h4>{{ $bill->user->name }}</h4>
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
                    <li>
                        <p>Bill Type </p>
                        <h4>
                            {{ Str::ucfirst(str_replace('_', ' ', $bill->bill_type)) }}
                        </h4>
                    </li>
                    <li>
                        <p>Service </p>
                        <h4>{{ $bill->service->name }}</h4>
                    </li>
                    <li>
                        <p>Amount </p>
                        <h4>{{ $bill->amount }}</h4>
                    </li>
                    <li>
                        <p>Due Date</p>
                        <h4>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
@endpush
