<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Technician Account Details</title>
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
        .button {
            background-color: #f43f5e;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .button:hover {
            background-color: #e3324f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appTitle }}</h1>
        </div>

        <p>Dear Technician,</p>
        <p>Your account has been created successfully. Below are your account details:</p>

        <div class="details">
            <p><strong>Account Number:</strong> {{ $accountNumber }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>

        <p>Please log in using the credentials above and change your password as soon as possible.</p>

        <p><a href="http://localhost:5173/technician/login" class="button">Log in to your account</a></p>

        <p>Thank you for joining us!</p>

        <div class="footer">
            <p>{{ $appAbout }}</p>
            <p>&copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
