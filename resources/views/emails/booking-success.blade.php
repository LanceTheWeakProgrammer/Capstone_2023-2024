<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success Confirmation</title>
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

        <p>Dear {{ $booking->user->full_name }},</p>
        <p>Your booking has been successfully created. Below are the details of your booking:</p>

        <div class="details">
            <p><strong>Technician:</strong> {{ $booking->technician->full_name }}</p>
            <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('F j, Y') }}</p>
            
            <p><strong>Vehicle(s):</strong> 
                @foreach ($booking->vehicleDetails as $vehicle)
                    {{ $vehicle->make }} {{ $vehicle->model }}@if(!$loop->last),@endif
                @endforeach
            </p>

            <p><strong>Service(s):</strong> 
                @foreach ($booking->services as $service)
                    {{ $service->name }}@if(!$loop->last),@endif
                @endforeach
            </p>

            <p><strong>Total Fee:</strong> ${{ number_format($booking->total_fee, 2) }}</p>
        </div>

        <p>Thank you for choosing our service. If you have any questions, feel free to contact us.</p>

        <div class="footer">
            <p>{{ $appAbout }}</p>
            <p>&copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
