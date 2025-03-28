@extends($thisModule . '::layouts.master')

@section('title', 'Refer Property')

@section('content')
    <div class="right_main_body_content members_page ">

        <div class="custom_table_wrapper">
            <div class="filter_table_head visitor-sec">
                <div class="right_filters">
                    <h2>Refer Property </h2>
                </div>
            </div>
            <div class="table-responsive visitors-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Refer From</th>
                            <th class="text-center">Refer To</th>
                            <th class="text-center">Phone</th>
                            <th class="text-center">Looking For</th>
                            <th class="text-center">BHK</th>
                            <th class="text-center">Min Budget</th>
                            <th class="text-center">Max Budget</th>
                            <th class="text-center">Preferred Location</th>
                            {{-- <th class="text-center">Property Status</th> --}}
                            {{-- <th class="text-center">Property Facing</th> --}}
                            {{-- <th class="text-center">Remark</th> --}}
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($refers && !$refers->isEmpty())
                            @foreach ($refers as $rproperty)
                                @php
                                    $sl++;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $rproperty->user->name }} </td>
                                    <td class="text-center">{{ $rproperty->name }} </td>
                                    <td class="text-center">{{ $rproperty->phone }}</td>
                                    <td class="text-center">{{ $rproperty->unit_type }}</td>
                                    <td class="text-center">{{ str_replace('BHK', '', $rproperty->bhk) }}</td>
                                    <td class="text-center">{{ $rproperty->min_budget }}</td>
                                    <td class="text-center">{{ $rproperty->max_budget }}</td>
                                    <td class="text-center">{{ $rproperty->location }}</td>
                                    {{-- <td class="text-center">{{ $rproperty->property_status }}</td> --}}
                                    {{-- <td class="text-center">{{ $rproperty->property_fancing }}</td> --}}
                                    {{-- <td class="text-center">{{ $rproperty->remark }}</td> --}}
                                    <td class="text-center">
                                        <div class="actions">
                                            <a href="{{ route($thisModule . '.refer_property.details', ['id' => $rproperty->id]) }}"
                                                id="{{ $rproperty->id }}">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                        stroke="#8077F5" stroke-width="1.25" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                        stroke="#8077F5" stroke-width="1.25" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>

                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center"> No Data Found </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
                <div class="table_bottom_box">
                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                        <div>
                            Showing {{ $refers->firstItem() }} to {{ $refers->lastItem() }} of
                            {{ $refers->total() }} results
                        </div>
                        <div>
                            {{ $refers->links('vendor.pagination.bootstrap-5') }}
                            {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('footer-script')
@endpush
