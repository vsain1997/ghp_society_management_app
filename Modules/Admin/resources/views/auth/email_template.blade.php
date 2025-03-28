<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            color: #51545e;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 20px;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #0066cc;
            color: #ffffff;
            text-align: center;
            padding: 30px 0;
        }

        .email-header h1 {
            font-size: 24px;
            margin: 0;
        }

        .email-body {
            padding: 30px;
            text-align: left;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px;
        }

        .email-body a {
            color: #0066cc;
            text-decoration: none;
        }

        .email-footer {
            background-color: #f4f4f7;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }

        #button {
            background-color: #0066cc;
            color: #fff;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
        }

        #button:hover {
            background-color: #004999;
        }
    </style>
</head>

<body>

    <div class="email-wrapper">
        <div class="email-content">
            <!-- Email Header -->
            <div class="email-header">
                <h1>Password Reset Request</h1>
            </div>

            <!-- Email Body -->
            <div class="email-body">
                <p>Hello,</p>
                <p>We received a request to reset your password. Click the button below to reset your password:</p>
                <p style="text-align: center;">
                    <a href="{{ url($thisModule . '/password/reset/' . $token) }}" id="button">Reset Password</a>
                </p>
                <p>If you did not request a password reset, please ignore this email or contact support if you have
                    questions.</p>
                <p>Thanks, <br> The {{ config('app.name') }} Team</p>
            </div>

            <!-- Email Footer -->
            <div class="email-footer">
                <p>If you’re having trouble clicking the button, copy and paste the URL below into your web browser:</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>

</body>

</html>
