<?php

namespace App\Mail;

// use App\Models\AppInfo;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Queue\SerializesModels;

// deprecated: guest booking functionality is no longer supported
// class SendGuestTokenMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     // deprecated: properties for guest booking
//     public $guest;
//     public $guestToken;
//     public $appInfo;

//     /**
//      * Create a new message instance.
//      */
//     // deprecated: constructor for guest booking mail
//     public function __construct($guest, $guestToken)
//     {
//         // $this->guest = $guest;
//         // $this->guestToken = $guestToken;
//         // $this->appInfo = AppInfo::first(); 
//     }

//     /**
//      * Get the message envelope.
//      */
//     // deprecated: envelope for guest booking mail
//     public function envelope(): Envelope
//     {
//         // return new Envelope(
//         //     subject: 'Your Guest Booking Token',
//         // );
//     }

//     /**
//      * Get the message content definition.
//      */
//     // deprecated: content for guest booking mail
//     public function content(): Content
//     {
//         // return new Content(
//         //     view: 'emails.send-guest-token',
//         //     with: [
//         //         'guestToken' => $this->guestToken,
//         //         'guest' => $this->guest,
//         //         'appTitle' => $this->appInfo->appTitle ?? 'Our Application',  
//         //         'appAbout' => $this->appInfo->appAbout ?? 'We strive to offer the best services for you.',
//         //     ],
//         // );
//     }

//     /**
//      * Get the attachments for the message.
//      *
//      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
//      */
//     // deprecated: no attachments for guest booking mail
//     public function attachments(): array
//     {
//         // return [];
//     }
// }
