<?php

namespace App\Notifications;

use App\Models\Plat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PublishedDish extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Plat $plat, public User $user)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Bonjour, ' .  $this->user->name )
                    ->action('Notification Action', url('/'))
                    ->line('Votre plat : "' . $this->plat->name . '" à bien été crée !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
