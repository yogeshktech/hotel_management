<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class BookingConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
   

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
    public function via(object $notifiable): array
    {
        return ['mail'];  // Add 'whatsapp' if custom channel
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Your booking is confirmed!')
            ->line('Details: ...');
    }

    // Custom WhatsApp (in controller or job)
    private function sendWhatsapp($booking)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create(
            'whatsapp:' . $booking->user->phone,  // Assume user has phone
            [
                'from' => env('TWILIO_WHATSAPP_FROM'),
                'body' => 'Booking confirmed! Details: ...'
            ]
        );
        // Send to admin too
    }
}
