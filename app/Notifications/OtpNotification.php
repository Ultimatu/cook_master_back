<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public string $otp;

    public string $name;

    public string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp, string $name, string $type)
    {
        $this->otp = $otp;
        $this->name = $name;
        $this->type = $type;
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
                    ->greeting("Salut $this->name,")
                    ->subject("Vérification de votre compte")
                    ->line("Votre code de vérification est : $this->otp")
                    ->line('Ce code expirera dans 10 minutes')
                    ->line('Si vous n\'avez pas demandé de code de vérification, veuillez ignorer cet email.')
                    ->salutation('Cordialement, ' . config('app.name'));

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
