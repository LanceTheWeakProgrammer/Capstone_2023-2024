<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Guest Token</title>
    <style>
        body {
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #f43f5e;
        }
        .token {
            background-color: #f43f5e;
            color: #fff;
            padding: 10px;
            font-size: 24px;
            text-align: center;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appTitle }}</h1>
        </div>

        <p>Hello, {{ $guest->guest_name }}</p> 
        <p>Here is your token to complete the booking process:</p>

        <div class="token">{{ $guestToken }}</div>

        <p>If you did not request this token, please ignore this email.</p>

        <div class="footer">
            <p>{{ $appAbout }}</p>
            <p>&copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
