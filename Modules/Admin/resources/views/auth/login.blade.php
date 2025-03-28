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
                        <h5>Welcome Back</h5>
                        <h3>Login to your account</h3>
                        <form id="loginForm" action="{{ route($thisModule . '.login.process') }}" method="POST">
                            @csrf
                            <div class="form_group">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                                <span id="emailErr" class="text-danger error"></span>
                            </div>
                            <div class="form_group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                <span id="passwordErr" class="text-danger error"></span>
                                <i toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></i>
                            </div>
                            <div class="form_group forgot-password">
                                <a href="{{ route($thisModule . '.password.request') }}">Forgot Password?</a>
                            </div>
                            <div class="sumit-btn-box">
                                <input type="submit" value="Login" id="submitForm" class="sumit-btn">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- @if (session('status') == 'error')
        <div class="alert alert-danger">
            {{ session('message') }}
        </div>
    @endif --}}

@endsection
@push('footer-script')
    <script>
        $(".toggle-password").click(function() {

$(this).toggleClass("fa-eye fa-eye-slash");
var input = $($(this).attr("toggle"));
if (input.attr("type") == "password") {
  input.attr("type", "text");
} else {
  input.attr("type", "password");
}
});


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

                var passwordInput = $("#password");
                var passwordErr = $("#passwordErr");
                var password = passwordInput.val().trim();

                if (password.length < 8) {
                    passwordErr.text("Please enter a valid password");
                    passwordErr.css("display", "block");

                    passwordInput.focus();
                    return; // Stop execution if password is invalid
                } else {
                    passwordErr.text("");
                    passwordErr.css("display", "none");
                }

                if (emailPattern.test(email) && password.length >= 8) {

                    // Refresh CSRF Token Before Submitting
                    $.get("{{ route('csrf.refresh') }}", function(data) {
                        $('meta[name="csrf-token"]').attr('content', data.token); // Update meta token
                        $("#loginForm").append('<input type="hidden" name="_token" value="' + data.token + '">');

                        // Submit the form after CSRF token update
                        $("#loginForm").submit();
                    });
                }

            });
        });
    </script>
@endpush
