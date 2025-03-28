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
                class="{{ request()->is($thisModule . '/dashboard*') ? 'active' : '' }}"
                >
                    <img src="{{ url($thisModule) }}/img/dashboard.svg" alt="Dashboard">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route($thisModule . '.settings.societies') }}"
                class="
                @if (request()->is($thisModule . '/settings*') && session('active_tab') == '#tab3-tab')
                    {{ 'active' }}
                @else
                {{ '' }}
                @endif
                ">
                    <i class="fa-solid fa-building" style="margin-right:8px"></i>
                    Societies
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/society/properties') }}"
                    class="{{ request()->is($thisModule . '/society/properties*') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/members.svg" alt="Resident Units"> --}}
                    <i class="fa-brands fa-hive" style="margin-right: 8px;"></i>
                    {{-- Resident Units --}}
                    Society Properties
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/member') }}">
                    <img src="{{ url($thisModule) }}/img/members.svg" alt="Members">
                    Members
                </a>
            </li>
            <li>
                <a href="{{ route($thisModule . '.settings.accessPermission') }}"
                class="
                @if (request()->is($thisModule . '/settings*') && session('active_tab') == '#tab4-tab')
                    {{ 'active' }}
                @else
                {{ '' }}
                @endif
                ">
                    <i class="fa-solid fa-user-lock" style="margin-right:8px"></i>
                    Access Permission
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/staff') }}"
                    class="{{ request()->is($thisModule . '/staff') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/members.svg" alt="Staff"> --}}
                    <i class="fa-solid fa-users" style="margin-right:8px"></i>
                    Staff
                </a>
            </li>
            <li class="item-list item-drop-btn
            {{ request()->is($thisModule . '/service*') ? 'show-items' : '' }}
            ">
                <a href="javascript:void(0);"
                    class="{{ request()->is($thisModule . '/service*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/service-providers.svg" alt="Services">
                    Services
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z" fill="#ffffff"></path> </g></svg></button>
                <div class="item-sub-menu">
                    <ul>
                        <li>
                            <a href="{{ url($thisModule . '/service-provider-category') }}"
                                class="{{ request()->is($thisModule . '/service-provider-category*') ? 'active' : '' }}">
                                {{-- <i class="fa-solid fa-list" style="margin-right: 8px;"></i> --}}
                                Service Category
                            </a>
                        </li>
                        <li>
                            <a href="{{ url($thisModule . '/service-provider') }}"
                                class="{{ request()->is($thisModule . '/service-provider') ? 'active' : '' }}">
                                {{-- <img src="{{ url($thisModule) }}/img/service-providers.svg" alt="Staff"> --}}
                                Service Provider
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- <li>
                <a href="{{ url($thisModule . '/service-provider-category') }}"
                    class="{{ request()->is($thisModule . '/service-provider-category*') ? 'active' : '' }}">
                    <i class="fa-solid fa-list" style="margin-right: 8px;"></i>
                    Service Category
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/service-provider') }}"
                    class="{{ request()->is($thisModule . '/service-provider') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/service-providers.svg" alt="Staff">
                    Service Provider
                </a>
            </li> --}}
            <li class="item-list item-drop-btn
            {{ request()->is($thisModule . '/complaint*') ? 'show-items' : '' }}
            ">
                <a href="javascript:void(0);"
                    class="{{ request()->is($thisModule . '/complaint*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/complaints.svg" alt="Complaints">
                    Complaints
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z" fill="#ffffff"></path> </g></svg></button>
                <div class="item-sub-menu">
                    <ul>
                        <li>
                            <a href="{{ url($thisModule . '/complaints-category') }}"
                                class="{{ request()->is($thisModule . '/complaints-category*') ? 'active' : '' }}">
                                {{-- <i class="fa-solid fa-list" style="margin-right: 8px;"></i> --}}
                                Complaints Category
                            </a>
                        </li>
                        <li>
                            <a href="{{ url($thisModule . '/complaint') }}"
                                class="{{ request()->is($thisModule . '/complaint') ? 'active' : '' }}">
                                {{-- <img src="{{ url($thisModule) }}/img/complaints.svg" alt="Complaints"> --}}
                                Complaints
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- <li>
                <a href="{{ url($thisModule . '/complaint') }}"
                    class="{{ request()->is($thisModule . '/complaint') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/complaints.svg" alt="Complaints">
                    Complaints
                </a>
            </li> --}}
            <li>
                <a href="{{ url($thisModule . '/event') }}"
                    class="{{ request()->is($thisModule . '/event*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/events.svg" alt="Events ">
                    Events
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/notice') }}"
                class="{{ request()->is($thisModule . '/notice*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/notice-board.svg" alt="Notice Board">
                    Notice Board
                </a>
            </li>
            {{-- <li>
                <a href="{{ url($thisModule . '/sos-category') }}"
                    class="{{ request()->is($thisModule . '/sos-category*') ? 'active' : '' }}">
                    <i class="fa-solid fa-list" style="margin-right: 8px;"></i>
                    SOS Category
                </a>
            </li> --}}
            <li class="item-list item-drop-btn
                {{ request()->is($thisModule . '/sos*') ? 'show-items' : '' }}
            ">
                <a href="javascript:void(0);"
                    class="{{ request()->is($thisModule . '/sos*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/sos.svg" alt="SOS">
                    SOS
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z" fill="#ffffff"></path> </g></svg></button>
                <div class="item-sub-menu">
                    <ul>
                        <li>
                            <a href="{{ url($thisModule . '/sos-category') }}"
                                class="{{ request()->is($thisModule . '/sos-category*') ? 'active' : '' }}">
                                {{-- <i class="fa-solid fa-list" style="margin-right: 8px;"></i> --}}
                                SOS Category
                            </a>
                        </li>
                        <li>
                            <a href="{{ url($thisModule . '/sos') }}"
                                class="{{ request()->is($thisModule . '/sos') ? 'active' : '' }}">
                                {{-- <img src="{{ url($thisModule) }}/img/sos.svg" alt="SOS"> --}}
                                SOS
                            </a>
                        </li>
                    </ul>
                </div>

            </li>
            <li class="item-list item-drop-btn
                {{ request()->is($thisModule . '/visitor*') ? 'show-items' : '' }}
            ">
                <a href="javascript:void(0);"
                    class="{{ request()->is($thisModule . '/visitor*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/visitors.svg" alt="Visitors">
                    Visitors
                </a>
                <button class="item-drop-btn"><svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L6.29289 9.70711C5.90237 9.31658 5.90237 8.68342 6.29289 8.29289C6.68342 7.90237 7.31658 7.90237 7.70711 8.29289L12 12.5858L16.2929 8.29289C16.6834 7.90237 17.3166 7.90237 17.7071 8.29289C18.0976 8.68342 18.0976 9.31658 17.7071 9.70711L12.7071 14.7071Z" fill="#ffffff"></path> </g></svg></button>
                <div class="item-sub-menu">
                    <ul>
                        <li>
                            <a href="{{ url($thisModule . '/visitor') }}"
                                class="{{ request()->is($thisModule . '/visitor') ? 'active' : '' }}">
                                {{-- <i class="fa-solid fa-list" style="margin-right: 8px;"></i> --}}
                                Resident Visitors
                            </a>
                        </li>
                        <li>
                            <a href="{{ url($thisModule . '/visitor-other') }}"
                                class="{{ request()->is($thisModule . '/visitor-other') ? 'active' : '' }}">
                                {{-- <img src="{{ url($thisModule) }}/img/sos.svg" alt="SOS"> --}}
                                Other Visitors
                            </a>
                        </li>
                    </ul>
                </div>

            </li>
            <li>
                <a href="{{ url($thisModule . '/property-listing') }}"
                    class="{{ request()->is($thisModule . '/property-listing') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/property-listings.svg" alt="Property Listing ">
                    Property Listing
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/poll') }}"
                    class="{{ request()->is($thisModule . '/poll*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/polls.svg" alt="Polls">
                    Polls
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/billing') }}"
                    class="{{ request()->is($thisModule . '/billing*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-wave" style="margin-right:8px"></i>
                    Billing
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/refer-property') }}"
                    class="{{ request()->is($thisModule . '/refer-property*') ? 'active' : '' }}">
                    <img src="{{ url($thisModule) }}/img/refer.svg" alt="Refer Property">
                    Refer Property
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/document') }}"
                    class="{{ request()->is($thisModule . '/document*') ? 'active' : '' }}">
                    {{-- <img src="{{ url($thisModule) }}/img/visitors.svg" alt="Document"> --}}
                    <i class="fa-solid fa-file" style="margin-right: 8px;"></i>
                    Documents
                </a>
            </li>
            <li>
                <a href="{{ url($thisModule . '/parcel') }}"
                    class="{{ request()->is($thisModule . '/parcel') ? 'active' : '' }}">
                    <i class="fa-solid fa-cube" style="margin-right: 8px;"></i>
                    Parcels
                </a>
            </li>
        </ul>
    </div>
    <div class="bottom_navigation">
        <ul>
            <li>
                <a href="javascript:void(0);" class="makeUserLogout">
                    <img src="{{ url($thisModule) }}/img/logout.svg" alt="Log Out">
                    Log Out
                </a>
            </li>
        </ul>
    </div>
</div>
