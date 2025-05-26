<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', 'Super Admin')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    {{-- icon --}}
    <link rel="apple-touch-icon" href="{{ asset($thisModule . '/img/fevi.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset($thisModule . '/img/logo.png') }}">
    {{-- default --}}
    <link rel="stylesheet" type="text/css" href="{{ asset($thisModule . '/css/bootstrap.min.css?' . time()) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset($thisModule . '/css/style.css?' . time()) }}">
    <script src="{{ asset($thisModule . '/js/jquery-3.6.0.min.js?' . time()) }}"></script>
    {{-- swal --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" />
    {{-- toast --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('common/css/toastr.css?' . time()) }}">
    <script src="{{ asset('common/js/toastr.min.js?' . time()) }}"></script>
    {{-- loader --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('common/css/common.css?' . time()) }}">
    {{-- icons --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('common/iconFontAwesome/css/all.min.css') }}">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    {{-- date + time selector --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $('#loader').css('width', '50%');
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toastr-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "2500",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    <script>
        // Create a reusable Swal instance with default settings
        const swalWithDefaults = Swal.mixin({
            allowOutsideClick: false, // Prevent closing when clicking outside
        });

        // Override global Swal function to use the custom settings
        window.Swal = swalWithDefaults;
    </script>



    <style>
        .noti-btn.has-unread {
            position: relative;
        }

        .noti-btn.has-unread::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background: red;
            border-radius: 50%;
        }

        .notiSocietyName {
            /* background-color: #ffffe0; */
            font-weight: 500 !important;
        }

        /* .noti-box ul li.unread .request-box {
        background: #e7e7f3;
        font-weight: bold;
    }

    .noti-box ul li .request-box {
        padding: 10px;
        border-bottom: 1px solid #e6e6e6;
        cursor: pointer;
    } */
    </style>

</head>

<body>

    @guest
    @yield('content')
    @endguest

    @auth
    <div id="loader"></div>
    <div id="blockOverlay"></div>
    <div class="page_wrapper">
        @include($thisModule . '::menus.sidebar')
        <div class="right_content_wrapper">
            @include($thisModule . '::menus.topbar')

            @yield('content')

        </div>
        @endauth

        <script src="{{ asset($thisModule . '/js/bootstrap.bundle.min.js?' . time()) }}"></script>
        <script src="{{ asset($thisModule . '/js/custom.js?' . time()) }}"></script>
        <script src="{{ asset('common/js/validate.js?' . time()) }}"></script>
        <!-- Select2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

        <script src="{{ asset('common/js/process-actions.js?' . time()) }}"></script>
        <script src="{{ asset('common/js/ajax-error.js?' . time()) }}"></script>

        @section('default-scripts')
        <!-- Overridable Section -->
        @endsection

        <script>
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            function fetchData(url, method = 'GET', headers = {}, body = null) {
                return fetch(url, {
                    method: method,
                    headers: headers,
                    body: body ? JSON.stringify(body) : null
                })
                    .then(response => response.json()) // Parse the response as JSON
                    .then(data => {
                        if (data.status === 'success') {
                            return data; // Return data if the status is success
                        } else {
                            toastr.error('Failed to load data');
                            return null; // Return null on failure
                        }
                    })
                    .catch(error => {
                        toastr.error('Failed, please try again');
                        return null; // Return null if there's an error
                    });
            }

            function reloadPage($sec = 2000) {
                setTimeout(function () {
                    location.reload();
                }, $sec);
            }

            function showSuccessMessage(message) {
                Swal.fire({
                    position: "top-end",
                    imageUrl: "{{ asset($thisModule . '/img/success_icon.svg') }}",
                    title: message,
                    showConfirmButton: false,
                    customClass: "toaset-popup",
                    timer: 2500
                });
            }

            function showErrorMessage(message) {
                Swal.fire({
                    position: "top-end",
                    imageUrl: "{{ asset($thisModule . '/img/close_icon.svg') }}",
                    title: message,
                    showConfirmButton: false,
                    customClass: "toaset-popup-error",
                    timer: 5000
                });
            }


            function checkEmail(adminEmail) {
                var adminId = $('#adminId').val();
                var isValidadminEmail = true;

                if (adminId <= 0) {
                    adminId = null;
                }

                $('#adminEmailError').text('');
                $.ajax({
                    {{-- url: '{{ route($thisModule .'.check.email') }}', --}}
                    url: '',
                        type: 'GET',
                            data: {
                        adminEmail: adminEmail,
                            adminId: adminId
                    }, headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
            `    success: function(response) {
                    console.log(response);
                    if (response.message === 'yes') {
                        $('#adminEmailError').text('This email already exists.');
                        isValidadminEmail = false;
                    } else if (response.message === 'no') {
                        $('#adminEmailError').text('Email not found.');
                    }
                },
                async: true,
                    });

                r`eturn isValidadminEmail;
            }
        </script>
        <script>
            function reloadWithoutQueryParams() {
                // Get the current URL without query parameters
                const url = window.location.origin + window.location.pathname;

                // Update the URL and reload the page
                window.location.href = url;
            }

            function changeSociety(select) {
                let societyId = select.value;

                // Send AJAX request to set session
                fetch('{{ route($thisModule . '.set.society.master') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        society_id: societyId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the current page to reflect the session change
                            // window.location.reload();
                            reloadWithoutQueryParams();
                        } else {
                            toastr.error('Failed to update Society');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Failed please try again');
                    });
            }
        </script>
        @if (Session::has('status') && Session::has('message'))
        <script>
            // Check if the page is being loaded from history (back button)
            if (!window.performance || window.performance.navigation.type !== 2) {
                toastr["{{ Session::get('status') }}"]("{{ Session::get('message') }}");
            }
        </script>
        @endif

        @if ($errors->any())
        <script>
            toastr.error("{{ $errors->first() }}");
        </script>
        @endif

        @stack('footer-script')

        <script>
            $('#loader').css('width', '100%');
            setTimeout(function () {
                $('#loader').fadeOut(500); // Fade out after all resources are loaded
            }, 100);
        </script>
        @auth
        <script>
            let offset = 0;
            const limit = 5;
            let renderedNotifications = [];
            // Fetch unread notifications every 30 seconds
            function fetchUnreadNotifications(render = false) {
                $.ajax({
                    url: '{{ route($thisModule . '.notification.getNotifications') }}',
                    type: 'GET',
                    data: {
                        offset: 0,
                        limit
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (notifications) {
                        var notifications = Object.values(notifications);
                        // Access totalCountNext from the first notification's data
                        var totalCountNext = notifications.length > 0 ? notifications[0].data.totalCountNext : 0;

                        if (notifications.length > 0) {
                            $('#notification-all-read').show();
                        }else{
                            $('#notification-all-read').hide();
                        }

                        // Handle button visibility based on totalCountNext
                        if (totalCountNext < 1) {
                            $("#load-more-btn").hide();
                        }
                        if (render) {
                            notifications.forEach(notification => {
                                renderedNotifications.push(notification.id);
                            });
                            renderNotifications(notifications, true); // Replace existing notifications
                        }
                        // Filter notifications where read_at is null (unread notifications)
                        const unreadNotifications = notifications.filter(n => n.read_at === null);
                        // Check if there are unread notifications
                        const hasUnread = notifications.some(n => n.read_at === null);
                        if (hasUnread) {
                            $(".noti-btn").addClass("has-unread");

                            // Only prepend notifications that haven't been rendered yet
                            const newUnreadNotifications = unreadNotifications.filter(notification =>
                                !renderedNotifications.includes(notification.id)
                            );

                            // Prepend the unread notifications that have not been rendered yet
                            if (newUnreadNotifications.length > 0) {
                                renderNotificationsPrepend(newUnreadNotifications);

                                // Add newly rendered notification IDs to the global array
                                newUnreadNotifications.forEach(notification => {
                                    renderedNotifications.push(notification.id);
                                });
                            }

                        } else {
                            $(".noti-btn").removeClass("has-unread");
                        }
                    },
                    error: function (error) {
                        console.error("Error fetching notifications:", error);
                    }
                });
            }

            // Render notifications inside .noti-box
            function renderNotifications(notifications, replace = false) {
                let notificationList = $(".noti-box ul");
                if (replace) notificationList.empty(); // Replace if it's a full refresh

                notifications.forEach(function (notification) {
                    if (notification && notification.data && notification.data.title) {
                        const isUnread = notification.read_at === null ? "unread" : "";
                        //
                        notificationList.append(`
                                <li class="${isUnread}" data-id="${notification.id}">
                                    <div class="request-box">
                                        <div class="info-noti">
                                            <p class="notiSocietyName">Society Name : ${notification.data.society_name}</p>
                                            <p>${notification.data.body}</p>
                                            ${notification.data.link || ""}
                                            <span>${timeAgo(new Date(notification.created_at))}</span>
                                        </div>
                                    </div>
                                </li>
                            `);
                    }
                });

                addMarkAsReadEvent();
            }

            function renderNotificationsPrepend(notifications) {
                let notificationList = $(".noti-box ul");
                // if (replace) notificationList.empty(); // Replace if it's a full refresh

                notifications.forEach(function (notification) {
                    if (notification && notification.data && notification.data.title) {
                        const isUnread = notification.read_at === null ? "unread" : "";
                        //
                        notificationList.prepend(`
                                <li class="${isUnread}" data-id="${notification.id}">
                                    <div class="request-box">
                                        <div class="info-noti">
                                            <p class="notiSocietyName">Society Name : ${notification.data.society_name}</p>
                                            <p>${notification.data.body}</p>
                                            ${notification.data.link || ""}
                                            <span>${timeAgo(new Date(notification.created_at))}</span>
                                        </div>
                                    </div>
                                </li>
                            `);
                    }
                });

                addMarkAsReadEvent();
            }

            // Mark a notification as read
            function addMarkAsReadEvent() {
                $(".noti-box ul li").off("click").on("click", function () {
                    const notificationId = $(this).data("id");
                    const listItem = $(this);

                    if (listItem.hasClass("unread")) {
                        $.ajax({
                            url: '{{ route($thisModule . '.notification.markAsRead') }}',
                            type: 'POST',
                            data: {
                                id: notificationId
                            },
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                            },
                            success: function () {
                                listItem.removeClass("unread"); // Mark visually as read
                            },
                            error: function (error) {
                                console.error("Error marking notification as read:", error);
                            }
                        });
                    }
                });
            }

            // Load more notifications
            function loadMoreNotifications() {
                offset += limit; // Increment the offset
                $.ajax({
                    url: '{{ route($thisModule . '.notification.getNotifications') }}',
                    type: 'GET',
                    data: {
                        offset,
                        limit
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (notifications) {
                        if (notifications.length > 0) {
                            renderNotifications(notifications, false); // Append new notifications
                        } else {
                            // Hide Load More button if no more notifications
                            $("#load-more-btn").hide();
                        }
                    },
                    error: function (error) {
                        console.error("Error loading more notifications:", error);
                    }
                });
            }

            // Time formatter
            function timeAgo(date) {
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                const intervals = [{
                    label: "year",
                    seconds: 31536000
                },
                {
                    label: "month",
                    seconds: 2592000
                },
                {
                    label: "day",
                    seconds: 86400
                },
                {
                    label: "hour",
                    seconds: 3600
                },
                {
                    label: "minute",
                    seconds: 60
                },
                {
                    label: "second",
                    seconds: 1
                },
                ];

                for (const interval of intervals) {
                    const count = Math.floor(diffInSeconds / interval.seconds);
                    if (count > 0) {
                        return `${count} ${interval.label}${count !== 1 ? "s" : ""} ago`;
                    }
                }
                return "just now";
            }

            // Show/Hide Notification Dropdown
            $(".noti-btn").on("click", function () {
                $(".noti-box").toggleClass("show");
            });

            // Initial fetch and interval setup
            $(document).ready(function () {
                fetchUnreadNotifications(true); // Initial fetch
                setInterval(fetchUnreadNotifications, 5000); // Fetch every 30 seconds

                // Load More button click
                $("#load-more-btn").on("click", function () {
                    loadMoreNotifications();
                });

                $("#notification-all-read").on("click", function (e) {
                    e.preventDefault(); // Prevent default action

                    $.ajax({
                        url: '{{ route($thisModule . '.notification.markAllAsRead') }}',
                        type: 'POST',
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function () {
                            // Mark all notifications as read visually
                            $(".noti-box ul li").removeClass("unread");
                        },
                        error: function (error) {
                            console.error("Error marking all notifications as read:", error);
                        }
                    });
                });

                $('.makeUserLogout').on('click', function (e) {
                    e.preventDefault(); // Prevent the default action (if it's a link)

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to log out?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, log me out!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If user confirms, redirect to the logout route
                            window.location.href = "{{ url($thisModule . '/logout') }}";
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // If user cancels, just do nothing (logout is restricted)
                            return;
                        }
                    });
                });

            });
        </script>
        <script>
            $(document).ready(function () {
                // Initialize tooltips for all elements with the class .info-icon
                $('.info-icon').each(function () {
                    const tooltip = new bootstrap.Tooltip(this, {
                        trigger: 'manual' // Manual control for the tooltip
                    });

                    // Toggle tooltip on click
                    $(this).on('click', function (e) {
                        e.stopPropagation(); // Prevent label behavior
                        const isVisible = $(this).attr('aria-describedby') !==
                            undefined; // Check visibility
                        if (isVisible) {
                            tooltip.hide(); // Hide tooltip if visible
                        } else {
                            tooltip.show(); // Show tooltip if not visible
                        }
                    });
                });

                // Hide all tooltips when clicking outside
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.info-icon').length) {
                        $('.info-icon').each(function () {
                            const tooltip = bootstrap.Tooltip.getInstance(this);
                            if (tooltip) {
                                tooltip.hide();
                            }
                        });
                    }
                });

                $(document).on('click', '.notificationMoreBtn', function () {
                    // Get data-societyid and data-href values
                    const societyId = $(this).data('societyid');
                    const href = $(this).data('href');
                    // loader add
                    $('#loader').css('width', '50%');
                    $('#loader').fadeIn();
                    $('#blockOverlay').fadeIn();

                    // Make an AJAX request to set the session value
                    $.ajax({
                        url: '{{ route($thisModule . '.set.society.master') }}', // Replace with your Laravel route to set the session value
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token for Laravel
                            society_id: societyId
                        },
                        success: function (response) {
                            if (response.success) {
                                // Redirect to the data-href link
                                window.location.href = href;
                            } else {
                                console.log('Failed to set society. Please try again.');
                            }
                        },
                        error: function () {
                            toastr.error('Error occurred. Please try again.');
                        },
                        complete: function () {
                            //loader removed
                            $('#loader').css('width', '100%');
                            $('#loader').fadeOut();
                            $('#blockOverlay').fadeOut();
                        }
                    });
                });

            });
        </script>
        @endauth

</body>
