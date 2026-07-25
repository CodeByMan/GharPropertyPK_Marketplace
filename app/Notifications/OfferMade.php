<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferMade extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Offer $offer)
    {
        //
    }

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
            ->subject('New property offer | GharProperty.pk')
            ->greeting("Hello {$notifiable->name},")
            ->line("An offer of PKR {$this->offer->amount} has been made on your listing.")
            ->action('Review offers for this listing', route('realtor.listing.show', $this->offer->listing_id))
            ->line('Review the offer in your GharProperty.pk workspace.')
            ->salutation('GharProperty.pk — Developed by Muhammad Ali Nawaz');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id,
            'listing_id' => $this->offer->listing_id,
            'amount' => $this->offer->amount,
            'bidder_id' => $this->offer->bidder_id,
        ];
    }
}
