@extends($thisModule . '::layouts.master')

@section('title', 'Property Listings')

@section('content')
    <div class="right_main_body_content members_page ">

        <div class="head_content">
            <div class="left_head">
                <h2>Property Listings</h2>
            </div>
        </div>


        <div class="custom_table_wrapper">
            <div class="filter_table_head visitor-sec">
                <div class="search_wrapper search-members-gstr">
                    <form action="{{ route($thisModule . '.property_listing.index') }}" method="GET">
                        {{-- @csrf --}}
                        <div class="input-group">
                            <input type="hidden" name="sid" value="{{ session('__selected_society__') }}">
                            <div class="filter-secl">
                                <select name="type" class="form-control">
                                    <option value="none" disabled>--Choose Type--</option>
                                    <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>Rent
                                    </option>
                                    <option value="sell" {{ request('type') == 'sell' ? 'selected' : '' }}>
                                        Sell
                                    </option>
                                </select>
                            </div>

                            <div class="search-full-box">
                                <input type="search" name="search" id="search" placeholder="Search"
                                    value="{{ request('search') }}">
                                <button type="submit" class="bg_theme_btn">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="right_filters">
                </div>
            </div>
            <div class="table-responsive visitors-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Owner</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Type </th>
                            <th class="text-center">Tower </th>
                            <th class="text-center">Floor </th>
                            <th class="text-center">Property Number </th>
                            <th class="text-center">Property Type </th>
                            <th class="text-center">Aval. Date </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($propertyListings && !$propertyListings->isEmpty())
                            @foreach ($propertyListings as $listing)
                                @php
                                    $sl++;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $listing->name }} </td>
                                    <td class="text-center">{{ $listing->phone }} </td>
                                    <td class="text-center">{{ $listing->type }} </td>
                                    <td class="text-center">{{ $listing->block_name }} </td>
                                    <td class="text-center">{{ $listing->floor }} </td>
                                    <td class="text-center">{{ $listing->unit_number }} </td>
                                    <td class="text-center">{{ $listing->unit_type }} </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($listing->date)->format('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="actions">
                                            <a href="{{ route($thisModule . '.property_listing.details', ['id' => $listing->id]) }}"
                                                id="{{ $listing->id }}">
                                                <img src="{{ url($thisModule) }}/img/eye.png" alt="eye">

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
                            Showing {{ $propertyListings->firstItem() }} to {{ $propertyListings->lastItem() }} of
                            {{ $propertyListings->total() }} results
                        </div>
                        <div>
                            {{ $propertyListings->links('vendor.pagination.bootstrap-5') }}
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
