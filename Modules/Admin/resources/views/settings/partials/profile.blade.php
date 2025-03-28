<h3>Personal Information </h3>
<div class="profile-image-wrapper">
    <div class="profile-pic">
        {{-- <img src="img/profile-main-img.png" alt="profile"> --}}
        @php
            $imagePathProfile =
                $userDetails->image && Storage::disk('public')->exists($userDetails->image)
                    ? 'storage/' . $userDetails->image
                    : 'storage/profile_picture/default.png'; // default image path
        @endphp

        <img id="showProPic" src="{{ asset($imagePathProfile) }}" alt="Profile Picture">

        <span class="uploadPic">
            <input type="file" name="profile_picture" id="profile_picture" onchange="loadFile(event)">
            <input type="hidden" name="isNewProfilePic" id="isNewProfilePic" value="0">
            <svg width="30" height="26" viewBox="0 0 30 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M15 18.1213C17.2091 18.1213 19 16.3305 19 14.1213C19 11.9122 17.2091 10.1213 15 10.1213C12.7909 10.1213 11 11.9122 11 14.1213C11 16.3305 12.7909 18.1213 15 18.1213Z"
                    stroke="white" stroke-width="1.5" />
                <path
                    d="M12.0373 24.788H17.9627C22.124 24.788 24.2053 24.788 25.7 23.808C26.3449 23.3853 26.9004 22.8399 27.3347 22.2027C28.3333 20.736 28.3333 18.692 28.3333 14.6067C28.3333 10.52 28.3333 8.47736 27.3347 7.01069C26.9004 6.37351 26.3449 5.82803 25.7 5.40536C24.74 4.77469 23.5373 4.54936 21.696 4.46936C20.8173 4.46936 20.0613 3.81603 19.8893 2.96936C19.7579 2.34918 19.4164 1.79341 18.9225 1.39596C18.4286 0.998513 17.8126 0.783771 17.1787 0.788026H12.8213C11.504 0.788026 10.3693 1.70136 10.1107 2.96936C9.93866 3.81603 9.18266 4.46936 8.304 4.46936C6.464 4.54936 5.26133 4.77603 4.3 5.40536C3.65549 5.82811 3.1005 6.37359 2.66666 7.01069C1.66666 8.47736 1.66666 10.52 1.66666 14.6067C1.66666 18.692 1.66666 20.7347 2.66533 22.2027C3.09733 22.8374 3.652 23.3827 4.3 23.808C5.79466 24.788 7.876 24.788 12.0373 24.788Z"
                    stroke="white" stroke-width="1.5" />
                <path d="M24.3333 10.1213H23" stroke="white" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </span>
    </div>
    <strong>
        Recommended dimensions: 200x200,
        <span>maximum file size: 5MB</span>
    </strong>
</div>
<form>
    @csrf
    <input id="loggerId" value="{{ $userDetails->id }}" type="hidden">
    <div class="info-box">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" placeholder="Eg. Jack Smith"
            value="{{ $userDetails->name }}">
        <span id="nameErr" class="mt-2 text-danger"></span>
        <div class="email-phone">
            <div class="phone">
                <label for="phone">Phone Number </label>
                <input type="number" name="phone" id="phone" placeholder="Eg. +91-99999-88888"
                    value="{{ $userDetails->phone }}">
                <span id="phoneErr" class="mt-2 text-danger"></span>
            </div>
            <div class="phone">
                <label for="email">Email </label>
                <input type="email" name="email" id="email" placeholder="info@email.com"
                    value="{{ $userDetails->email }}">
                <span id="emailErr" class="mt-2 text-danger"></span>
            </div>
        </div>
    </div>
    <button type="submit" id="updateprofilebtn">Submit</button>
</form>

@push('profile_section')
    <script>
        $(document).ready(function() {

            // update profile
            $("#updateprofilebtn").click(function(event) {
                event.preventDefault();
                $(".text-danger").text("");

                var loggerId = $("#loggerId").val();
                var name = $("#name").val();
                var phone = $("#phone").val();
                var email = $("#email").val();

                if (name === "" || !isNaN(name)) {
                    $("#nameErr").text("Please enter a valid Name");
                    return false;
                }

                if (phone === "") {
                    $("#phoneErr").text("Please enter valid Phone number");
                    return false;
                } else if (!/^\d{10}$/.test(phone)) {
                    $("#phoneErr").text("Please enter a valid 10-digit Phone number");
                    return false;
                }

                if (email === "") {
                    $("#emailErr").text("Please enter correct email");
                    return false;
                }else {
                    // Regular expression to validate email format
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        $("#emailErr").text("Please enter a valid email address");
                        return false;
                    }
                }

                updateProfile();
            });
        });

        var loadFile = function(event) {
            var isValidated =
                validateImage(
                    fileUploadId = 'profile_picture',
                    filePreviewId = ['showProPic', 'showProPic2'],
                    maxAllowedSizeMb = 5);

            if (isValidated) {
                $('#isNewProfilePic').val(1);
            }

        };

        function validateImage(fileUploadId, filePreviewId = [], maxAllowedSizeMb = 2, requiredUpload =
            false, checkWidth = null, checkHeight = null) {

            var fileInput = $('#' + fileUploadId)[0];
            var filePath = fileInput.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            var adjustedSizeKB = 30;
            var maxSize = (maxAllowedSizeMb * 1024) + adjustedSizeKB; // in KB
            var fileSize = fileInput.files[0].size / 1024; //in KB

            //validate is required
            if (requiredUpload) {
                if (!fileInput.files || !fileInput.files[0]) {
                    toastr.warning('Please Upload Image File');
                    return false;
                }
            }

            // validate file extension
            if (!allowedExtensions.exec(filePath)) {
                toastr.warning('Only .jpeg , .jpg , .png are allowed');
                fileInput.value = ''; // Clear the file input
                return false;
            }

            // validate file size
            if (fileSize > maxSize) {
                toastr.warning('Maximum file size is ' + maxAllowedSizeMb + ' MB');
                fileInput.value = ''; // Clear the file input
                return false;
            }

            //validate dimensions
            if (checkWidth != null || checkHeight != null) {

                var img = new Image();
                img.src = URL.createObjectURL(fileInput.files[0]);
                // If validation passes, preview the image
                img.onload = function() {

                    var width = img.width;
                    var height = img.height;

                    if (checkWidth != null && checkHeight != null && width != checkWidth && height !=
                        checkHeight) {

                        toastr.warning('Recommended dimensions : 200x200');
                        fileInput.value = '';
                        return false;
                    } else
                    if (checkWidth != null && width != checkWidth) {

                        toastr.warning('Recommended dimensions : 200x200');
                        fileInput.value = '';
                        return false;
                    } else if (checkHeight != null && height != checkHeight) {

                        toastr.warning('Recommended dimensions : 200x200');
                        fileInput.value = '';
                        return false;
                    }
                }
            }

            //preview image
            if (filePreviewId.length > 0) {
                // Preview image
                for (let x = 0; x < filePreviewId.length; x++) {

                    var output = document.getElementById(filePreviewId[x]);
                    output.src = URL.createObjectURL(fileInput.files[0]);
                    output.onload = function() {
                        URL.revokeObjectURL(output.src); // Free up memory once image is loaded
                    };
                }
            }
            return true;
        }

        function updateProfile() {
            // show loader
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            var name = $("#name").val();
            var phone = $("#phone").val();
            var email = $("#email").val();
            var profile_picture = $("#profile_picture")[0].files[0]; // Get the file object
            var imageref = $("#isNewProfilePic").val();
            if (imageref != '1') {
                profile_picture = null;
                imageref = 0;
            }

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = new FormData();
            formData.append('type', 'profileUpdate');
            formData.append('name', name);
            formData.append('phone', phone);
            formData.append('email', email);
            formData.append('profile_picture', profile_picture); // Append the file object directly
            formData.append('isNewProfilePic', imageref); // Append the file object directly

            $.ajax({
                url: '{{ route($thisModule . '.profile.update') }}',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    // show toast message
                    if (response.hasOwnProperty('status')) {
                        toastr[response.status](response.message);
                    }
                    $('#isNewProfilePic').val(0); //reset for next profile Img Upload
                    $('#topBarUsername').text(name);
                },
                error: function(xhr, status, error) {

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    toastr.error('Error occurred ! Please try again');
                }
            });
        }
    </script>
@endpush
