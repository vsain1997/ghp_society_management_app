@extends($thisModule . '::layouts.master')

@section('title', 'Society')

@section('content')
    <div class="right_main_body_content society_detail_page">
        <div class="head_content">
            <div class="left_head">
                <h2>
                    <a href="{{ route($thisModule . '.settings') }}">
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
                        <p>Society Name</p>
                        <h4>{{ $society->name }}</h4>
                    </li>
                    <li>
                        <p>Street Address</p>
                        <h4>{{ $society->location }}</h4>
                    </li>
                    <li>
                        <p>City</p>
                        <h4>{{ $society->city }}</h4>
                    </li>
                    <li>
                        <p>State/UT</p>
                        <h4>{{ $society->state }}</h4>
                    </li>
                    <li>
                        <p>Pin Code</p>
                        <h4>{{ $society->pin }}</h4>
                    </li>
                    <li>
                        <p>Society Contact </p>
                        <h4>{{ $society->contact }}</h4>
                    </li>
                    <li>
                        <p>Society Email </p>
                        <h4>{{ $society->email }}</h4>
                    </li>
                    <li>
                        <p>Registration Number </p>
                        <h4>{{ $society->registration_num }}</h4>
                    </li>
                    <li>
                        <p>Society Type </p>
                        <h4>{{ Str::ucfirst($society->type) }}</h4>
                    </li>
                    {{-- <li>
                        <p>Total Properties</p>
                        <h4>{{ count($society->blocks) }}</h4>
                    </li> --}}
                    <li>
                        <p>Status</p>
                        <h4>{{ Str::ucfirst($society->status) }}</h4>
                    </li>
                    {{-- <li>
                        <p>Assigned Admin</p>
                        <h4>{{ isset($society->assigned_admin->name) ? $society->assigned_admin->name : '' }}</h4>
                    </li> --}}
                    <li>
                        <p>Total Area (Sq. Ft.)</p>
                        <h4>{{ $society->total_area }}</h4>
                    </li>
                    <li>
                        <p>Total Towers</p>
                        <h4>{{ $society->total_towers }}</h4>
                    </li>
                    {{-- <li> --}}
                        <p>Amenities</p>
                        <h4>{{ $society->amenities }}</h4>
                    {{-- </li> --}}
                </ul>
                </table>
            </div>
        </div>
        <div class="bg-card mt-2">
            <div class="society_details">
                <h6>Emergency Contacts</h6>
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($society->society_contacts as $contactInfo)
                            <tr>
                                <td>{{ $contactInfo->name }}</td>
                                <td>{{ Str::ucfirst($contactInfo->designation) }}</td>
                                <td>{{ $contactInfo->phone }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </table>
            </div>
        </div>
        <div class="bg-card mt-2">
            <div class="society_details">
                <h6>Towers Details</h6>
                @php
                    // Grouping blocks by block_name
                    $groupedBlocks = $society->blocks->groupBy('name');
                @endphp
                <table class="table table-responsive">
                    <tbody>
                        @foreach ($groupedBlocks as $blockName => $blocks)
                            @if (isset($prevBlock) && $prevBlock != $blockName)
                                <tr>
                                    <td class="text-center" colspan="6">&nbsp;</td>
                                </tr>
                            @endif

                            @php $prevBlock = $blockName ?? null; @endphp

                            <tr style="background-color: #F8F8F8;">
                                <th colspan="4"> Tower : {{ $blockName }}</th>
                                @php
                                    $firstBlock = $blocks->first();
                                @endphp
                                <th colspan="2"> Total Floors : {{ $firstBlock->total_floor }}</th>
                            </tr>
                            <tr>
                                <th>Property Number</th>
                                <th>Floor</th>
                                <th>Property Type</th>
                                <th>Size (sq. ft.)</th>
                                <th>BHK</th>
                                <th>Property Status</th>
                            </tr>
                            @foreach ($blocks as $block)
                            @php
                            $isExist = \App\Models\Member::where('block_id', $block->id)->exists();
                            $stts_text = $isExist ? 'Occupied' : 'Vacant';
                            $stts_class = $isExist ? 'green-status-btn' : 'exp-status-btn';
                        @endphp

                                <tr>
                                    <td>{{ $block->property_number }}</td>
                                    <td>{{ $block->floor }}</td>
                                    <td>{{ ucfirst($block->unit_type) }}</td>
                                    <td>
                                        @if($block->unit_size > 0)
                                        {{ $block->unit_size }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($block->unit_type == 'residential')
                                            {{ $block->bhk }}
                                        @endif
                                    </td>
                                    <td class="{{ $stts_class }}">
                                        <button class="text-uppercase">{{ $stts_text }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
@endpush
