<div class="top_header">
    <button class="togger_sidebar d-inline-block d-xl-none">
        <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M21.6667 8C21.6667 8.35362 21.5262 8.69276 21.2761 8.94281C21.0261 9.19286 20.687 9.33333 20.3333 9.33333H1.66667C1.31305 9.33333 0.973906 9.19286 0.723857 8.94281C0.473809 8.69276 0.333334 8.35362 0.333334 8C0.333334 7.64638 0.473809 7.30724 0.723857 7.05719C0.973906 6.80714 1.31305 6.66667 1.66667 6.66667L20.3333 6.66667C20.687 6.66667 21.0261 6.80714 21.2761 7.05719C21.5262 7.30724 21.6667 7.64638 21.6667 8ZM16.3333 14.6667C16.3333 15.0203 16.1929 15.3594 15.9428 15.6095C15.6928 15.8595 15.3536 16 15 16H1.66667C1.31305 16 0.973906 15.8595 0.723857 15.6095C0.473809 15.3594 0.333334 15.0203 0.333334 14.6667C0.333334 14.313 0.473809 13.9739 0.723857 13.7239C0.973906 13.4738 1.31305 13.3333 1.66667 13.3333H15C15.3536 13.3333 15.6928 13.4738 15.9428 13.7239C16.1929 13.9739 16.3333 14.313 16.3333 14.6667ZM11 1.33333C11 1.68696 10.8595 2.02609 10.6095 2.27614C10.3594 2.52619 10.0203 2.66667 9.66667 2.66667L1.66667 2.66667C1.31305 2.66667 0.973906 2.52619 0.723857 2.27614C0.473809 2.02609 0.333334 1.68696 0.333334 1.33333C0.333334 0.979712 0.473809 0.640574 0.723857 0.390526C0.973906 0.140477 1.31305 9.53674e-07 1.66667 9.53674e-07L9.66667 9.53674e-07C10.0203 9.53674e-07 10.3594 0.140477 10.6095 0.390526C10.8595 0.640574 11 0.979712 11 1.33333Z"
                fill="black" />
        </svg>
    </button>
    <div class="left_header">

        <select name="socities" id="socities"
            class="form-select topSocietySelectElement @if (Request::is('superadmin/settings*')) {{ 'd-none' }} @endif"
            onchange="changeSociety(this)">
            @php $__scount__ = 0; @endphp
            @foreach ($__societies__ as $__society__)
                @if (!session('__selected_society__'))
                    @if ($__scount__ == 0)
                        <option value="{{ $__society__->id }}" selected>{{ $__society__->name }}</option>
                        @php
                            session(['__selected_society__' => $__society__->id]);
                        @endphp
                    @endif
                @endif

                @if (session('__selected_society__') == $__society__->id)
                    <option value="{{ $__society__->id }}" selected>{{ $__society__->name }}</option>
                @else
                    <option value="{{ $__society__->id }}">{{ $__society__->name }}</option>
                @endif
                @php $__scount__++; @endphp
            @endforeach
        </select>

    </div>
    <div class="right_header">
        <ul>
            <li class="notification-box">
                <a href="javascript:void(0);" class="noti-btn">
                    <img src="{{ asset($thisModule) }}/img/notification.svg" alt="notification">
                </a>
                <div class="noti-box">
                    <div class="noti-heading">
                        <h3>Notifications</h3>
                        <a href="javascript:void(0);" id="notification-all-read">Mark all as read</a>
                    </div>
                    <ul>
                    </ul>
                    <button id="load-more-btn" class="load-more">Load More</button>
                </div>
            </li>
            <li>
                <div class="btn-group">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        @php
                            $userDetails = Auth::user();
                            $imagePathProfile =
                                $userDetails->image && Storage::disk('public')->exists($userDetails->image)
                                    ? 'storage/' . $userDetails->image
                                    : 'storage/profile_picture/default.png'; // default image path
                        @endphp

                        <img id="showProPic2" src="{{ asset($imagePathProfile) }}" alt="profile">

                        <strong>
                            <span class="fw-bold" id="topBarUsername">{{ Auth::user()->name }}</span>

                            <span>{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</span>
                        </strong>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="profile_info">
                            <h5>{{ Auth::user()->name }}</h5>
                            <p>{{ Auth::user()->email }}</p>
                        </div>
                        <ul>
                            <li>
                                <a href="{{ url($thisModule . '/settings') }}">Settings</a>
                            </li>
                            <li>
                                {{-- <a href="{{ url($thisModule . '/logout') }}">Logout</a> --}}
                                <a href="javascript:void(0);" class="makeUserLogout">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
