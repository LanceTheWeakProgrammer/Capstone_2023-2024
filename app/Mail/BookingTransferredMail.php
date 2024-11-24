<?php

namespace App\Mail;

// use App\Models\Guest;
// use App\Models\AppInfo;
// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Queue\SerializesModels;

// deprecated: guest booking transfer functionality is no longer supported
// class BookingTransferredMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     // deprecated: properties for guest booking transfer
//     public $guest;
//     public $appInfo;

//     /**
//      * Create a new message instance.
//      */
//     // deprecated: constructor for booking transfer mail
//     public function __construct(Guest $guest)
//     {
//         // $this->guest = $guest;
//         // $this->appInfo = AppInfo::first();
//     }

//     /**
//      * Get the message envelope.
//      */
//     // deprecated: envelope for booking transfer mail
//     public function envelope(): Envelope
//     {
//         // return new Envelope(
//         //     subject: 'Booking Transfer Confirmation',
//         // );
//     }

//     /**
//      * Get the message content definition.
//      */
//     // deprecated: content for booking transfer mail
//     public function content(): Content
//     {
//         // return new Content(
//         //     view: 'emails.booking-transfer',
//         //     with: [
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
//     // deprecated: no attachments for booking transfer mail
//     public function attachments(): array
//     {
//         // return [];
//     }
// }
