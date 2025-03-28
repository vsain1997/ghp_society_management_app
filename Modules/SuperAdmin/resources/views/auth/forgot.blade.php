@extends($thisModule . '::layouts.master')

@section('title', 'Login')

@section('content')
    <div class="login_wrapper">
        <div class="container">
            <div class="login_box">
                <div class="login_image d-none d-md-block">
                    <img src="{{ asset($thisModule . '/img/logimg.png') }}" alt="login">
                </div>
                <div class="login_content">
                    <div class="login_cnt_box">
                        <img src="{{ asset($thisModule . '/img/logo.png') }}" alt="logo" class="login-logo">
                        <h3>Forgot Password</h3>
                        <form id="requestForm" action="{{ route($thisModule . '.password.email') }}" method="POST">
                            @csrf
                            @if (Session::has('status') && Session::has('message') && Session::get('status') == 'success')
                                <div class="form_group">
                                    <h5>
                                        Please check your email to reset password.
                                    </h5>
                                </div>
                            @else
                                <div class="form_group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                    <span id="emailErr" class="text-danger error"></span>
                                </div>
                                <div class="form_group forgot-password">
                                    <a href="{{ route($thisModule . '.login.form') }}">Sign In</a>
                                </div>

                                <div class="sumit-btn-box">
                                    <input type="submit" value="Submit" id="submitForm" class="sumit-btn">
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('footer-script')
    <script>
        $(document).ready(function() {
            $("#submitForm").click(function(event) {
                event.preventDefault();
                var emailInput = $("#email");
                var emailErr = $("#emailErr");
                var email = emailInput.val().trim();
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    emailErr.text("Please enter a valid email address");
                    emailErr.css("display", "block");
                } else {
                    emailErr.css("display", "none");
                }

                if (emailPattern.test(email)) {
                    $("#requestForm").submit();
                }

            });
        });
    </script>
@endpush
