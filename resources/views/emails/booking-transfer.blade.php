<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Transfer Confirmation</title>
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
        .details {
            margin: 20px 0;
        }
        .details p {
            margin: 5px 0;
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
        <p>Your booking(s) have been successfully transferred to your newly registered account.</p>

        <p>If you did not initiate this request or if you suspect unauthorized access, please contact us immediately using the details below:</p>

        <p><strong>Contact Support:</strong> support@example.com or +123-456-7890</p>

        <p>We’re glad to have you on board. Enjoy our services!</p>

        <div class="footer">
            <p>{{ $appAbout }}</p>
            <p>&copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
