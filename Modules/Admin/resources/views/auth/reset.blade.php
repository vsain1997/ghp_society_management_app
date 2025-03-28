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
                    <h3>Reset Password</h3>
                    <form id="resetForm" action="{{ route($thisModule . '.password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form_group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <span id="newPassErr" class="text-danger error"></span>
                        </div>

                        <div class="form_group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" required>
                            <span id="cnfNewPassErr" class="text-danger error"></span>
                        </div>

                        <div class="sumit-btn-box">
                            <input type="submit" value="Reset Password" id="submitForm" class="sumit-btn">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-script')
<script>
    $(document).ready(function () {
        $("#submitForm").click(function (event) {

            event.preventDefault();
            var newPass = $("#password").val();
            var cnfNewPass = $("#password_confirmation").val();

            if (newPass == "") {
                $("#newPassErr").text("Please enter new password");
                return false;
            }

            if (newPass.length < 8) {
                $("#newPassErr").text("Length Mininum 8 characters");
                return false;
            }

            if (cnfNewPass == "") {
                $("#cnfNewPassErr").text("Please enter confirm password");
                return false;
            }

            if (cnfNewPass != newPass) {
                $("#cnfNewPassErr").text("Passwords doesn't match");
                return false;
            }

            if (cnfNewPass.length > 7) {
                $("#resetForm").submit();
            }

        });
    });
</script>
@endpush