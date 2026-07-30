<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Password')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password', url('/reset-password/' . $this->token))
            ->line('Jika Anda tidak meminta reset password, tidak perlu melakukan tindakan apapun.')
            ->line('Link reset password ini akan kadaluarsa dalam 60 menit.');
    }
}
