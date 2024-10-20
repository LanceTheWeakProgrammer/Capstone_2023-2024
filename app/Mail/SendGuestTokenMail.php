<?php

namespace App\Mail;

use App\Models\AppInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendGuestTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;
    public $guestToken;
    public $appInfo;

    /**
     * Create a new message instance.
     */
    public function __construct($guest, $guestToken)
    {
        $this->guest = $guest;
        $this->guestToken = $guestToken;
        $this->appInfo = AppInfo::first(); 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Guest Booking Token',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-guest-token',
            with: [
                'guestToken' => $this->guestToken,
                'guest' => $this->guest,
                'appTitle' => $this->appInfo->appTitle ?? 'Our Application',  
                'appAbout' => $this->appInfo->appAbout ?? 'We strive to offer the best services for you.',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
