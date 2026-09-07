<?php

namespace App\Notifications;

use App\Models\FlightAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlightPriceAlertNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly FlightAlert $alert, private readonly float $price) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $alert = $this->alert;

        return (new MailMessage)
            ->subject("Price alert: {$alert->origin} \u{2192} {$alert->destination} is now {$alert->currency} {$this->price}")
            ->greeting('Your flight price target has been met!')
            ->line("{$alert->origin} to {$alert->destination} on {$alert->departure_date->toFormattedDateString()}"
                .($alert->return_date ? " (return {$alert->return_date->toFormattedDateString()})" : ' (one-way)'))
            ->line("Current price: {$alert->currency} {$this->price}")
            ->line("Your target price: {$alert->currency} {$alert->target_price}")
            ->action('View alert', url("/flight-alerts/{$alert->id}"))
            ->line('We will keep monitoring this route unless you deactivate the alert.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'flight_alert_id' => $this->alert->id,
            'origin' => $this->alert->origin,
            'destination' => $this->alert->destination,
            'price' => $this->price,
            'currency' => $this->alert->currency,
        ];
    }
}
