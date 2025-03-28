<div class="bg-card">
    <div class="custom_form">
        <form>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="currPass">Current Password</label>
                        <input type="text" name="currPass" id="currPass" class="form-control">
                        <span id="currPassErr" class="mt-2 text-danger"></span>
                    </div>
                </div>
                <div class="col"></div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="newPass">New Password</label>
                        <input type="text" name="newPass" id="newPass" class="form-control">
                        <span id="newPassErr" class="text-danger"></span>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="cnfNewPass">Confirm New Password</label>
                        <input type="text" name="cnfNewPass" id="cnfNewPass" class="form-control">

                        <span id="cnfNewPassErr" class="text-danger"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <input type="submit" value="Change Password" class="bg_theme_btn" id="passwordChange">
                </div>
            </div>
        </form>
    </div>
</div>
@push('password_section')
    <script>
        $(document).ready(function() {

            // update password
            $("#passwordChange").click(function() {
                event.preventDefault();
                $(".text-danger").text("");

                var currPass = $("#currPass").val();
                var newPass = $("#newPass").val();
                var cnfNewPass = $("#cnfNewPass").val();

                if (currPass == "") {
                    $("#currPassErr").text("Please enter current password");
                    return false;
                }

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
                    $("#cnfNewPassErr").text("Passwords do not match");
                    return false;
                }

                updatePassword();
            });
        });

        function updatePassword() {
            // show loader
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            var currPass = $("#currPass").val();
            var cnfNewPass = $("#cnfNewPass").val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{ route($thisModule . '.profile.update') }}',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    type: 'passwordUpdate',
                    currPass: currPass,
                    cnfNewPass: cnfNewPass,
                },
                success: function(response) {

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    // show toast message
                    if (response.hasOwnProperty('status')) {
                        toastr[response.status](response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    showErrorMessage("invalid current password try again.");
                }
            });
        }
    </script>
@endpush
