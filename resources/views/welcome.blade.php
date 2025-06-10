<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Styles -->
        <style>
            *{box-sizing:border-box;margin:0;padding:0;}
                @import url('https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap');
                body{ font-family: "PT Sans", sans-serif;background-color:#f5f6fa;}
                h1{font-size:2.8rem;font-weight:700;margin-bottom:20px;}
                .loginPage{display:flex;width:100%;height:100vh;background:url('storage/logologin.png')0 0 no-repeat;background-size:cover;align-items:center;justify-content:center;padding:15px;}
                .loginForm{display:flex;flex-direction:column;width:1100px;background:#fff;border-radius:20px;padding:35px;}
                .loginLogo img{max-width:290px;object-fit:contain;}
                .loginLogo{background:#E2D8FB;height:220px;display:flex;align-items:center;justify-content:center;border-radius:15px;}
                .loginBtn{margin:25px 0 0;display:flex;align-items:center;justify-content:space-between;}
                .loginBtn a{display:block;width:48.5%;background:#4900FF99;text-align:center;padding:17px 10px;border-radius:8px;color:#fff;font-size:20px;font-weight:600;text-transform:uppercase;text-decoration:none;}
                .loginBtn a:hover,.loginBtn a:focus{background:#000;}
                /* responsive */
                @media(max-width:1199px){.loginBtn a {font-size: 17px;}
                }
                @media(max-width:991px){.loginBtn a{padding:17px 6px;font-size:13px;}
                }
                @media(max-width:767px){.loginForm{padding:15px;border-radius:10px;}
                .loginBtn{flex-wrap:wrap;gap:10px;}
                .loginBtn a{width:100%;}
                }
                @media(max-width:360px){.loginLogo img{max-width:220px;object-fit:contain;}
                    .loginBtn a {
                        padding: 17px 4px;
                        font-size: 11px;
                    }
                }
            /* ! tailwindcss v3.2.4 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;
        </style>
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
                    @auth
                        <a href="{{ url('/home') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="loginPage">
                <div class="loginForm">
                    <div class="loginLogo">
                        <img src="{{ asset('storage/logologin.png') }}" alt="">
                    </div>
                    <div class="loginBtn">
                    <a href="{{ route('superadmin.login.form') }}">Login as Super Admin</a>
                    <a target="_blank" href="{{ route('admin.login.form') }}">Login as Admin / Collection Department</a>
                    </div>
                </div>
            </div>
               
            </div>
        </div>
    </body>
</html>
