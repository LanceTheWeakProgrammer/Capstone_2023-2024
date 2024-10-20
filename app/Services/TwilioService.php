<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    }

    public function sendSMS($to, $message)
    {
        try {
            return $this->twilio->messages->create(
                $to,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Twilio SMS Error: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
    
}
