@extends($thisModule . '::layouts.master')

@section('title', 'Settings')

@section('content')

    <div class="right_main_body_content">
        <div class="head_content">
            <div class="left_head">
                <h2>Settings</h2>
            </div>
        </div>
        <div class="setting_tab">
            <ul class="nav" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" onclick="setTab()" id="tab1-tab" data-bs-toggle="tab"
                        data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1"
                        aria-selected="true">Personal
                        Information</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" onclick="setTab()" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2"
                        type="button" role="tab" aria-controls="tab2" aria-selected="false">Change
                        Password</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" onclick="setTab()" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3"
                        type="button" role="tab" aria-controls="tab3" aria-selected="false">Societies
                        Management</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" onclick="setTab()" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4"
                        type="button" role="tab" aria-controls="tab4" aria-selected="false">Access &
                        Permissions</button>
                </li>
            </ul>
            <div class="tab-content" id="setting_tabs">
                <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    {{--  profile --}}
                    @include($thisModule . '::settings.partials.profile')
                </div>
                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    {{-- change-pass --}}
                    @include($thisModule . '::settings.partials.password')
                </div>
                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    {{-- society --}}
                    @include($thisModule . '::settings.partials.society')
                </div>
                <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                    {{-- access permission --}}
                    @include($thisModule . '::settings.partials.permission')
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

            if (activeTab == '#tab4-tab') {

                $('.topSocietySelectElement').removeClass('d-xl-none');
                $('.topSocietySelectElement').removeClass('d-lg-none');
                $('.topSocietySelectElement').removeClass('d-md-none');
                $('.topSocietySelectElement').removeClass('d-none');
            } else {

                $('.topSocietySelectElement').addClass('d-none');
            }

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

        $(document).ready(function() {
            var activeTab = '{{ session('active_tab') }}';

            if (activeTab == '#tab4-tab') {

                $('.topSocietySelectElement').removeClass('d-xl-none');
                $('.topSocietySelectElement').removeClass('d-lg-none');
                $('.topSocietySelectElement').removeClass('d-md-none');
                $('.topSocietySelectElement').removeClass('d-none');
            } else {

                $('.topSocietySelectElement').addClass('d-none');
            }
        });
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
    @stack('society_section')
    @stack('permission_section')
@endpush
