@extends($thisModule . '::layouts.master')

@section('title', 'Settings')

@section('content')

    <div class="right_main_body_content members_page ">
        <div class="personal-info">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                        role="tab" aria-controls="home" aria-selected="true"><img src="img/solar_layers-bold.png"
                            alt=""> Update Profile </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                        role="tab" aria-controls="profile" aria-selected="false"><img src="img/solar_layers-bold.png"
                            alt="">Update Password </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button"
                        role="tab" aria-controls="contact" aria-selected="false"><img src="img/solar_layers-bold.png"
                            alt="">Notification Setting </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @include($thisModule . '::settings.partials.profile')
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    @include($thisModule . '::settings.partials.password')
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    @include($thisModule . '::settings.partials.notification_settings')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        function setTab() {
            var activeTab = '#' + event.target.id; //.replace('-tab', '');
            console.log(activeTab);

            $.ajax({
                url: "{{ route($thisModule . '.set.active.tab') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    activeTab: activeTab
                },
                success: function(response) {
                    console.log(activeTab);
                }
            });
        }
    </script>
    @if (session('active_tab'))
        <script>
            $(document).ready(function() {
                var activeTab = '{{ session('active_tab') }}';
                console.log('get', activeTab);
                var tab = new bootstrap.Tab($(activeTab)[0]);
                tab.show();
            });
        </script>
    @endif

    @stack('profile_section')
    @stack('password_section')
    @stack('notification_settings_section')
@endpush
