<div class="side_header">
    <div class="logo_wrapper">
        <a href="#">
            <img src="{{ url($thisModule) }}/img/logo.png" alt="logo">
        </a>
    </div>
    <div class="navigation_wrapper">
        <ul>
            <li>
                <a href="{{ url($thisModule . '/dashboard') }}"
                    class="{{ request()->is($thisModule . '/dashboard*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/dashboard.svg" alt="Dashboard">
                    Dashboard
                </a>
            </li>
            @if (hasPermissionLike('society.'))
            <li>
                <a href="{{ url($thisModule . '/society/properties') }}"
                    class="{{ request()->is($thisModule . '/society/properties*') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/members.svg" alt="Resident Units"> --}}
                    <i class="fa-brands fa-hive" style="margin-right: 8px;"></i>
                    {{-- Resident Units --}}
                    Society Properties
                </a>
            </li>
            @endif
            @if (hasPermissionLike('member.'))
            <li>
                <a href="{{ url($thisModule . '/member') }}"
                    class="{{ request()->is($thisModule . '/member') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/members.svg" alt="Staff">
                    Member
                </a>
            </li>
            @endif
            @if (hasPermissionLike('staff.'))
            <li>
                <a href="{{ url($thisModule . '/staff') }}"
                    class="{{ request()->is($thisModule . '/staff') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/members.svg" alt="Staff"> --}}
                    <i class="fa-solid fa-users" style="margin-right:8px"></i>
                    Staff
                </a>
            </li>
            @endif

            @if (hasPermissionLike('service_provider.') || hasPermissionLike('service_provider_category.'))
            <li class="item-list item-drop-btn
                {{ request()->is($thisModule . '/service*') ? 'show-items' : '' }}
                ">
                <a href="javascript:void(0);" class="{{ request()->is($thisModule . '/service*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/service-providers.svg" alt="Services">
                    Services
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z"
                                fill="#ffffff"></path>
                        </g>
                    </svg></button>
                <div class="item-sub-menu">
                    <ul>
                        @if (hasPermissionLike('service_provider_category.'))
                        <li>
                            <a href="{{ url($thisModule . '/service-provider-category') }}"
                                class="{{ request()->is($thisModule . '/service-provider-category*') ? 'active' : '' }}">
                                Service Category
                            </a>
                        </li>
                        @endif
                        @if (hasPermissionLike('service_provider.'))
                        <li>
                            <a href="{{ url($thisModule . '/service-provider') }}"
                                class="{{ request()->is($thisModule . '/service-provider') ? 'active' : '' }}">
                                Service Provider
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if (hasPermissionLike('complaints_category.') || hasPermissionLike('complaints.'))
            <li class="item-list item-drop-btn
                {{ request()->is($thisModule . '/complaint*') ? 'show-items' : '' }}
                ">
                <a href="javascript:void(0);" class="{{ request()->is($thisModule . '/complaint*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/complaints.svg" alt="Complaints">
                    Complaints
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z"
                                fill="#ffffff"></path>
                        </g>
                    </svg></button>
                <div class="item-sub-menu">
                    <ul>
                        @if (hasPermissionLike('complaints_category.'))
                        <li>
                            <a href="{{ url($thisModule . '/complaints-category') }}"
                                class="{{ request()->is($thisModule . '/complaints-category*') ? 'active' : '' }}">
                                Complaints Category
                            </a>
                        </li>
                        @endif
                        @if (hasPermissionLike('complaints.'))
                        <li>
                            <a href="{{ url($thisModule . '/complaint') }}"
                                class="{{ request()->is($thisModule . '/complaint') ? 'active' : '' }}">
                                Complaints
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if (hasPermissionLike('event.'))
            <li>
                <a href="{{ url($thisModule . '/event') }}"
                    class="{{ request()->is($thisModule . '/event*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/events.svg" alt="Events ">
                    Events
                </a>
            </li>
            @endif
            @if (hasPermissionLike('notice.'))
            <li>
                <a href="{{ url($thisModule . '/notice') }}"
                    class="{{ request()->is($thisModule . '/notice*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/notice-board.svg" alt="Notice Board">
                    Notice Board
                </a>
            </li>
            @endif
            @if (hasPermissionLike('sos_category.') || hasPermissionLike('sos.'))
            <li class="item-list item-drop-btn
                {{ request()->is($thisModule . '/sos*') ? 'show-items' : '' }}
                ">
                <a href="javascript:void(0);" class="{{ request()->is($thisModule . '/sos*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/sos.svg" alt="SOS">
                    SOS
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z"
                                fill="#ffffff"></path>
                        </g>
                    </svg></button>
                <div class="item-sub-menu">
                    <ul>
                        @if (hasPermissionLike('sos_category.'))
                        <li>
                            <a href="{{ url($thisModule . '/sos-category') }}"
                                class="{{ request()->is($thisModule . '/sos-category*') ? 'active' : '' }}">
                                SOS Category
                            </a>
                        </li>
                        @endif
                        @if (hasPermissionLike('sos.'))
                        <li>
                            <a href="{{ url($thisModule . '/sos') }}"
                                class="{{ request()->is($thisModule . '/sos') ? 'active' : '' }}">
                                SOS
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif


            @if (hasPermissionLike('visitor.'))
            <li class="item-list item-drop-btn
                {{ request()->is($thisModule . '/visitor*') ? 'show-items' : '' }}
                ">
                <a href="javascript:void(0);" class="{{ request()->is($thisModule . '/visitor*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/visitors.svg" alt="Visitor">
                    visitors
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z"
                                fill="#ffffff"></path>
                        </g>
                    </svg></button>
                <div class="item-sub-menu">
                    <ul>
                        @if (hasPermissionLike('visitor.'))
                        <li>
                            <a href="{{ url($thisModule . '/visitor') }}"
                                class="{{ request()->is($thisModule . '/visitor') ? 'active' : '' }}">
                                Resident Visitors
                            </a>
                        </li>
                        @endif
                        @if (hasPermissionLike('visitor.'))
                        <li>
                            <a href="{{ url($thisModule . '/visitor-other') }}"
                                class="{{ request()->is($thisModule . '/visitor-other') ? 'active' : '' }}">
                                Other Visitors
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if (hasPermissionLike('property_listing.'))
            <li>
                <a href="{{ url($thisModule . '/property-listing') }}"
                    class="{{ request()->is($thisModule . '/property-listing') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/property-listings.svg" alt="Property Listing ">
                    Property Listing
                </a>
            </li>
            @endif
            @if (hasPermissionLike('poll.'))
            <li>
                <a href="{{ url($thisModule . '/poll') }}"
                    class="{{ request()->is($thisModule . '/poll*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/polls.svg" alt="Polls">
                    Polls
                </a>
            </li>
            @endif
            @if (hasPermissionLike('billing.'))
            <li>
                <a href="{{ url($thisModule . '/billing') }}"
                    class="{{ request()->is($thisModule . '/billing*') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/refer.svg" alt="Billing"> --}}
                    <i class="fa-solid fa-money-bill-wave" style="margin-right:8px"></i>
                    Billing
                </a>
            </li>
            @endif
            @if (hasPermissionLike('refer_property.'))
            <li>
                <a href="{{ url($thisModule . '/refer-property') }}"
                    class="{{ request()->is($thisModule . '/refer-property*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/refer.svg" alt="Refer Property">
                    Refer Property
                </a>
            </li>
            @endif
            @if (hasPermissionLike('document.'))
            <li>
                <a href="{{ url($thisModule . '/document') }}"
                    class="{{ request()->is($thisModule . '/document*') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/visitors.svg" alt="Document"> --}}
                    <i class="fa-solid fa-file" style="margin-right: 8px;"></i>
                    Documents
                </a>
            </li>
            @endif
            @if (hasPermissionLike('parcel.'))
            <li>
                <a href="{{ url($thisModule . '/parcel') }}"
                    class="{{ request()->is($thisModule . '/parcel') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/visitors.svg" alt="Parcel"> --}}
                    <i class="fa-solid fa-cube" style="margin-right: 8px;"></i>
                    Parcels
                </a>
            </li>
            @endif
        </ul>
    </div>
    <div class="bottom_navigation">
        <ul>
            @if (config('app.env') === 'local')
            {{-- remove in production --}}
            <li class="d-none">
                <a style="outline: none" href="javascript:void(0);" id="triggerQueueBtn" class="btn btn-primary">
                    Run
                </a>
                <script>
                    $(document).ready(function () {
                        $('#triggerQueueBtn').click(function () {
                            $('#loader').css('width', '50%');
                            $('#loader').fadeIn();
                            $('#blockOverlay').fadeIn();
                            $.ajax({
                                url: '/trigger-queue-process',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for POST requests
                                },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        toastr.success(response.message);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function () {
                                    toastr.error('An error occurred while processing the request.');
                                },
                                complete: function () {
                                    //loader removed
                                    window.location.reload();
                                    $('#loader').css('width', '100%');
                                    // $('#loader').fadeOut();
                                    // $('#blockOverlay').fadeOut();

                                }
                            });
                        });
                    });
                </script>
            </li>
            @endif
            <li>
                <a href="javascript:void(0);" class="makeUserLogout">
                    <img src="{{ url($thisModule) }}/img/logout.svg" alt="Log Out">
                    Log Out
                </a>
            </li>
        </ul>
    </div>
</div>