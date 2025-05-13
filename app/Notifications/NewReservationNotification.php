<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Transaction $transaction
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Room Reservation Request')
            ->line('A new room reservation request has been submitted.')
            ->line('Guest: ' . $this->transaction->customer->name)
            ->line('Room: ' . $this->transaction->room->number)
            ->line('Check-in: ' . $this->transaction->check_in->format('M d, Y'))
            ->line('Check-out: ' . $this->transaction->check_out->format('M d, Y'))
            ->action('View Reservation', url('/receptionist/reservations/' . $this->transaction->id))
            ->line('Please review and process this reservation request.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New reservation request from ' . $this->transaction->customer->name,
            'transaction_id' => $this->transaction->id,
            'room_number' => $this->transaction->room->number,
            'check_in' => $this->transaction->check_in->format('Y-m-d'),
            'check_out' => $this->transaction->check_out->format('Y-m-d'),
            'url' => '/receptionist/reservations/' . $this->transaction->id
        ];
    }
} 