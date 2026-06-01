<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginOtpNotification extends Notification
{
    public function __construct(private string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de connexion Tiny Soccer Predictions')
            ->greeting('Bonjour !')
            ->line('Voici votre code de connexion :')
            ->line("**{$this->code}**")
            ->line('Ce code est valable **10 minutes**.')
            ->line('Si vous n\'avez pas demandé ce code, ignorez cet email.');
    }
}
