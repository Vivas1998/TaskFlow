<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Restablece tu contraseña de TaskFlow')
            ->greeting('Hola, '.$notifiable->first_name)
            ->line('Hemos recibido una solicitud para cambiar la contraseña de tu cuenta.')
            ->action('Cambiar contraseña', $resetUrl)
            ->line("Este enlace caduca en {$expiration} minutos y solo puede utilizarse una vez.")
            ->line('Si no has solicitado este cambio, puedes ignorar este mensaje.');
    }
}
