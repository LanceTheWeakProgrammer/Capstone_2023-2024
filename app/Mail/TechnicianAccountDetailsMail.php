<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AppInfo;

class TechnicianAccountDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $accountNumber;
    public $password;
    public $appInfo;

    /**
     * Create a new message instance.
     */
    public function __construct($accountNumber, $password)
    {
        $this->accountNumber = $accountNumber;
        $this->password = $password;
        $this->appInfo = AppInfo::first(); 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Technician Account Details',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.technician-account-details',
            with: [
                'accountNumber' => $this->accountNumber,
                'password' => $this->password,
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
